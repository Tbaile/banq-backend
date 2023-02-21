variable "WWWUID" {
    default = 1000
}

variable "WWWGID" {
    default = 1000
}

target "base" {
    target  = "production"
    context = "."
}

target "app" {
    inherits   = ["base"]
    dockerfile = "containers/php/Dockerfile"
    tags       = [
        "ghcr.io/thegardenboys/banq-backend-app:latest"
    ]
    cache-from = [
        "ghcr.io/thegardenboys/banq-backend-app:master-cache"
    ]
    output = ["type=docker"]
}

target "app-development" {
    inherits = ["app"]
    target   = "development"
    tags     = [
        "ghcr.io/thegardenboys/banq-backend-app:development"
    ]
    args = {
        WWWUID = WWWUID
        WWWGID = WWWGID
    }
}

target "web" {
    inherits   = ["base"]
    dockerfile = "containers/nginx/Dockerfile"
    tags       = [
        "ghcr.io/thegardenboys/banq-backend-web:latest"
    ]
    cache-from = [
        "ghcr.io/thegardenboys/banq-backend-web:master-cache"
    ]
    output = ["type=docker"]
}

target "web-development" {
    inherits = ["web"]
    tags     = [
        "ghcr.io/thegardenboys/banq-backend-web:development"
    ]
}

target "testing" {
    inherits = ["app-development"]
    target   = "testing"
    tags     = [
        "ghcr.io/thegardenboys/banq-backend-app:testing"
    ]
}

group "production" {
    targets = ["app", "web"]
}

group "development" {
    targets = ["app-development", "web-development"]
}

group "default" {
    targets = ["development"]
}
