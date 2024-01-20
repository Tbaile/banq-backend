target "app-production" {
    target     = "production"
    dockerfile = "containers/php/Dockerfile"
    tags       = [
        "banq-app"
    ]
    cache-from = "gha"
}

## Production Build Specs
target "web-production" {
    target     = "production"
    dockerfile = "containers/nginx/Dockerfile"
    tags       = [
        "banq-web"
    ]
    cache-from = "gha"
}

group "production" {
    targets = ["app-production", "web-production"]
}
