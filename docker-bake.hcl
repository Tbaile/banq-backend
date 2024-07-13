target "app-production" {
    target     = "production"
    dockerfile = "containers/php/Dockerfile"
}

target "web-production" {
    target     = "production"
    dockerfile = "containers/nginx/Dockerfile"
}
