## Production Build Specs
target "base" {
    target  = "production"
    context = "."
    output  = ["type=docker"]
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
}

group "production" {
    targets = ["app", "web"]
}

## Development Build Specs
variable "WWWUID" {
    default = 1000
}
variable "WWWGID" {
    default = 1000
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

target "web-development" {
    inherits = ["web"]
    tags     = [
        "ghcr.io/thegardenboys/banq-backend-web:development"
    ]
}

group "development" {
    targets = ["app-development", "web-development"]
}

## Testing Build Specs
target "testing" {
    inherits = ["app-development"]
    target   = "testing"
    tags     = [
        "ghcr.io/thegardenboys/banq-backend-app:testing"
    ]
}

group "default" {
    targets = ["development"]
}

group "all" {
    targets = ["production", "development", "testing"]
}
