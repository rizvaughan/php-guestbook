FROM mysql:8.0

# Copy init script to docker-entrypoint-initdb.d
# This runs automatically on first container startup
COPY init.sql /docker-entrypoint-initdb.d/

# Optional: Add custom my.cnf for performance
RUN echo '[mysqld]\ncharacter-set-server=utf8mb4\ncollation-server=utf8mb4_unicode_ci\nmax_connections=200' > /etc/mysql/conf.d/custom.cnf

EXPOSE 3306
