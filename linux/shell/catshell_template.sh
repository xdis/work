#!/bin/bash

# Shell Env
SHELL_NAME="shell_template.sh"
SHELL_DIR="c:/opt/shell"
SHELL_LOG="${SHELL_DIR}/${SHELL_NAME}.log"
LOCK_FILE="/tmp/${SHELL_NAME}.lock"

#Write Log 
shell_log(){
    LOG_INFO=$1
    echo "$(date "+%Y-%m-%d") $(date "+%H-%M-%S") : ${SHELL_NAME} : ${LOG_INFO}" >> ${SHELL_LOG}
}

shell_log "shell beginning ,write log test"
shell_log "shell succes ,write log test"