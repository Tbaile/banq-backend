target "base" {
    target  = "production"
    context = "."
}

target "app" {
    inherits   = ["base"]
    dockerfile = "containers/php/Dockerfile"
}

target "app-development" {
    inherits = ["app"]
    tags     = [
        "ghcr.io/thegardenboys/banq-backend-app:latest"
    ]
    cache-from = [
        "ghcr.io/thegardenboys/banq-backend-app:master-cache"
    ]
    output = ["type=docker"]
}

target "web" {
    inherits   = ["base"]
    dockerfile = "containers/nginx/Dockerfile"
}

target "web-development" {
    inherits = ["web"]
    tags     = [
        "ghcr.io/thegardenboys/banq-backend-web:latest"
    ]
    cache-from = [
        "ghcr.io/thegardenboys/banq-backend-web:master-cache"
    ]
    output = ["type=docker"]
}

target "app-testing" {
    inherits = ["app-development"]
    target   = "testing"
    tags     = [
        "ghcr.io/thegardenboys/banq-backend-app:testing"
    ]
}

group "production" {
    targets = ["app", "web"]
}

group "default" {
    targets = ["app-development", "web-development"]
}
