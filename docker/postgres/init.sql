CREATE DATABASE ls_identity_db;
CREATE DATABASE ls_marketplace_profile_db;

CREATE USER ls_identity_user WITH PASSWORD 'ls_identity_pwd';
CREATE USER ls_marketplace_profile_user WITH PASSWORD 'ls_marketplace_profile_pwd';

GRANT ALL PRIVILEGES ON DATABASE ls_identity_db TO ls_identity_user;
GRANT ALL PRIVILEGES ON DATABASE ls_marketplace_profile_db TO ls_marketplace_profile_user;

\c ls_identity_db
GRANT ALL ON SCHEMA public TO ls_identity_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO ls_identity_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO ls_identity_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO ls_identity_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO ls_identity_user;

\c ls_marketplace_profile_db
GRANT ALL ON SCHEMA public TO ls_marketplace_profile_db;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO ls_marketplace_profile_db;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO ls_marketplace_profile_db;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO ls_marketplace_profile_db;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO ls_marketplace_profile_db;
