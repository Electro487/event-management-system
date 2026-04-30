// Minimal API client for incremental MVC -> API migration.
// Stores JWT in localStorage and attaches Authorization header.

(() => {
  const STORAGE_KEY = "ems.jwt";
  const BASE = "/EventManagementSystem/public";

  function getToken() {
    try {
      let token = localStorage.getItem(STORAGE_KEY);
      if (!token) {
        // Fallback to cookie if localStorage is empty (for MVC compatibility)
        const match = document.cookie.match(new RegExp('(^| )ems_jwt=([^;]+)'));
        if (match) token = match[2];
      }
      return token;
    } catch {
      return null;
    }
  }

  function setToken(token) {
    try {
      localStorage.setItem(STORAGE_KEY, token);
      // Also set as cookie for PHP backend bridge
      document.cookie = `ems_jwt=${token}; path=/; max-age=${7 * 24 * 60 * 60}; samesite=lax`;
    } catch {
      // ignore
    }
  }

  function clearToken() {
    try {
      localStorage.removeItem(STORAGE_KEY);
      // Clear cookie
      document.cookie = 'ems_jwt=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
    } catch {
      // ignore
    }
  }

  async function apiFetch(path, { method = "GET", body, headers = {} } = {}) {
    const token = getToken();
    const url = BASE + path;

    const isFormData = body instanceof FormData;
    const fetchOptions = {
      method,
      headers: {
        ...(isFormData ? {} : { "Content-Type": "application/json" }),
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
        ...headers,
      },
      body: isFormData ? body : (body === undefined ? undefined : JSON.stringify(body)),
    };

    const res = await fetch(url, fetchOptions);

    let json = null;
    try {
      json = await res.json();
    } catch {
      // non-json response
    }

    if (res.status === 401) {
      // Token missing/expired/invalid: clear and force logout to refresh session/token
      clearToken();
      window.location.href = BASE + "/logout?reason=unauthorized";
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

