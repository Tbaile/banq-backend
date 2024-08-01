target "app-production" {
    target     = "production"
    dockerfile = "containers/php/Dockerfile"
    platforms = ["linux/amd64", "linux/arm64"]
}

target "web-production" {
    target     = "production"
    dockerfile = "containers/nginx/Dockerfile"
    platforms = ["linux/amd64", "linux/arm64"]
}
