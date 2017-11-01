# server-based syntax
# ======================
# Defines a single server with a list of roles and multiple properties.

set :docker_compose_port_range, 80..90

server "164.132.102.62", user: "aitor", roles: %w{app web}