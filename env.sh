# Source this before running moodle-docker commands: . ~/siraj-local/env.sh
export MOODLE_DOCKER_WWWROOT=$HOME/siraj-local/moodle
export MOODLE_DOCKER_DB=mariadb
export MOODLE_DOCKER_PHP_VERSION=8.3
export MOODLE_DOCKER_WEB_PORT=127.0.0.1:8000
export COMPOSE_PROJECT_NAME=siraj
