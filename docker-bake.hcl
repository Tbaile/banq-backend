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
        "ghcr.io/thegardenboys/banq-server-app:latest"
    ]
    cache-from = [
        "ghcr.io/thegardenboys/banq-server-app:master-cache"
    ]
    output  = ["type=docker"]
}

target "web" {
    inherits   = ["base"]
    dockerfile = "containers/nginx/Dockerfile"
}

target "web-development" {
    inherits = ["web"]
    tags     = [
        "ghcr.io/thegardenboys/banq-server-web:latest"
    ]
    cache-from = [
        "ghcr.io/thegardenboys/banq-server-web:master-cache"
    ]
    output  = ["type=docker"]
}

group "production" {
    targets = ["app", "web"]
}

group "default" {
    targets = ["app-development", "web-development"]
}
