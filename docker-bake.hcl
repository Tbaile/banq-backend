target "app-production" {
    target     = "production"
    dockerfile = "containers/php/Dockerfile"
    tags       = [
        "banq-app"
    ]
}

## Production Build Specs
target "web-production" {
    target     = "production"
    dockerfile = "containers/nginx/Dockerfile"
    tags       = [
        "banq-web"
    ]
}

group "production" {
    targets = ["app-production", "web-production"]
}
