// Minimal API client for incremental MVC -> API migration.
// Stores JWT in localStorage and attaches Authorization header.

(() => {
  const STORAGE_KEY = "ems.jwt";
  const BASE = "/EventManagementSystem/public";

  function getToken() {
    try {
      return localStorage.getItem(STORAGE_KEY);
    } catch {
      return null;
    }
  }

  function setToken(token) {
    try {
      localStorage.setItem(STORAGE_KEY, token);
    } catch {
      // ignore
    }
  }

  function clearToken() {
    try {
      localStorage.removeItem(STORAGE_KEY);
    } catch {
      // ignore
    }
  }

  async function apiFetch(path, { method = "GET", body, headers = {} } = {}) {
    const token = getToken();
    const url = BASE + path;

    const res = await fetch(url, {
      method,
      headers: {
        "Content-Type": "application/json",
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
        ...headers,
      },
      body: body === undefined ? undefined : JSON.stringify(body),
    });

    let json = null;
    try {
      json = await res.json();
    } catch {
      // non-json response
    }

    if (res.status === 401) {
      // Token missing/expired/invalid: clear and allow caller to redirect
      clearToken();
    }

    if (!res.ok) {
      const msg =
        json?.error?.message ||
        json?.message ||
        `Request failed with HTTP ${res.status}`;
      const err = new Error(msg);
      err.status = res.status;
      err.payload = json;
      throw err;
    }

    return json;
  }

  window.emsApi = {
    getToken,
    setToken,
    clearToken,
    apiFetch,
    base: BASE,
  };
})();

