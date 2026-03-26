-- Create a default Admin (Password: admin123)
INSERT INTO users (fullname, email, password, role, is_verified) 
VALUES ('System Admin', 'admin@ems.com', 'admin123', 'admin', 1);

-- Create a default Organizer (Password: org123)
INSERT INTO users (fullname, email, password, role, is_verified) 
VALUES ('Event Organizer', 'organizer@ems.com', 'org123', 'organizer', 1);