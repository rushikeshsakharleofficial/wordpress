#!/bin/bash
SERVER_NAME="domain1.com domain2.com domain3.com"
SERVER_ALIAS="existing.com"

# Handle multiple domains in SERVER_NAME
# Split SERVER_NAME into array
IFS=' ' read -r -a SERVERS <<< "$SERVER_NAME"

# First domain is the actual ServerName
export SERVER_NAME="${SERVERS[0]}"

# The rest (plus existing SERVER_ALIAS) become ServerAlias
if [ "${#SERVERS[@]}" -gt 1 ]; then
    ALIASES="${SERVERS[@]:1}"
    if [ -n "$SERVER_ALIAS" ]; then
        export SERVER_ALIAS="$ALIASES $SERVER_ALIAS"
    else
        export SERVER_ALIAS="$ALIASES"
    fi
fi

echo "SERVER_NAME: $SERVER_NAME"
echo "SERVER_ALIAS: $SERVER_ALIAS"
