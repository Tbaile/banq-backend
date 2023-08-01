## Production Build Specs
target "base" {
    target  = "production"
    context = "."
    output  = ["type=docker"]
}

target "app" {
    inherits   = ["base"]
    dockerfile = "containers/php/Dockerfile"
}

target "web" {
    inherits   = ["base"]
    dockerfile = "containers/nginx/Dockerfile"
}

## Development Build Specs
variable "UID" {
    default = 1000
}
variable "GID" {
    default = 1000
}

target "app-development" {
    inherits = ["app"]
    target   = "development"
    tags     = [
        "banq-development-app"
    ]
    args = {
        UID = UID
        GID = GID
    }
}

target "web-development" {
    inherits = ["web"]
    tags     = [
        "banq-development-web"
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
        "banq-testing"
    ]
}

## Production Build Specs
target "app-production" {
    inherits = ["app"]
    target   = "production"
    tags     = [
        "banq-app"
    ]
}

target "web-production" {
    inherits = ["web"]
    target   = "production"
    tags     = [
        "banq-web"
    ]
}

group "production" {
    targets = ["app-production", "web-production"]
}

group "all" {
    targets = ["development", "testing", "production"]
}

group "default" {
    targets = ["development"]
}
