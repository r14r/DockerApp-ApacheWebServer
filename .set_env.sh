export   ENV_NAME=Default
export   ENV_HOME=$PWD
export VSCODE_ENV=$Apache-WebServer

export STARSHIP_CONFIG=$ENV_HOME/.config/starship.toml
eval "$(starship init bash)"

. venv env        init
. venv vscode     init

