target "base" {
    cache-from = [
        "type=gha"
    ]
    output = [
        "type=docker"
    ]
}

target "app-production" {
    inherits = ["base"]
    target     = "production"
    dockerfile = "containers/php/Dockerfile"
    tags       = [
        "ghcr.io/thegardenboys/banq-backend-app:latest"
    ]
}

target "web-production" {
    inherits = ["base"]
    target     = "production"
    dockerfile = "containers/nginx/Dockerfile"
    tags       = [
        "ghcr.io/thegardenboys/banq-backend-web:latest"
    ]
}

group "default" {
    targets = ["app-production", "web-production"]
}
