## Requirements
> Docker

## Installation

```bash
make up
```

## Using

The entrance to the container
```bash
make bash container=container-name
```

Monitor service logs
```bash
make logs service=service-name
```

Stop container
```bash
make stop service=service-name
```

Start container
```bash
make start service=service-name
```

Build all services
```bash
make build
```

Build specific service
```bash
make build service=service-name
```

Up service
```bash
make up service=service-name
```

Status of running containers
```bash
make status
```

If you need more information, use
```bash
make help
```