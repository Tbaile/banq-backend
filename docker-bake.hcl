target "base" {
    target  = "production"
    context = "."
    output  = ["type=docker"]
}

target "app" {
    inherits   = ["base"]
    dockerfile = "containers/php/Dockerfile"
}

target "app-production" {
    inherits = ["app"]
    tags     = [
        "ghcr.io/thegardenboys/banq-server-app:latest"
    ]
}

target "web" {
    inherits   = ["base"]
    dockerfile = "containers/nginx/Dockerfile"
}

target "web-production" {
    inherits = ["web"]
    tags     = [
        "ghcr.io/thegardenboys/banq-server-web:latest"
    ]
}

group "default" {
    targets = ["app-production", "web-production"]
}
