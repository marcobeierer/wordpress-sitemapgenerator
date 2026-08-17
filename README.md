# Sitemap Generator

## wp-env

Needs unsafe Docker (without user namespaces):

```
alias unsafedocker='DOCKER_HOST=unix:///run/docker-unsafe.sock'
```

```
unsafedocker wp-env start
unsafedocker wp-env stop

# reset password
unsafedocker wp-env run cli wp user update admin --user_pass=password
```
