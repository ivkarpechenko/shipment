php() {
  echo "Running PHP as app user ..."
  su app -c "php $*"
}

# Determine size of a file or total size of a directory
fs() {
  if du -b /dev/null >/dev/null 2>&1; then
    local arg=-sbh
  else
    local arg=-sh
  fi
  # shellcheck disable=SC2199
  if [[ -n "$@" ]]; then
    du $arg -- "$@"
  else
    du $arg .[^.]* ./*
  fi
}

# Commonly used aliases
alias ..="cd .."
alias ...="cd ../.."
# shellcheck disable=SC2139
alias symfony="php ${ROOT}/bin/console"
