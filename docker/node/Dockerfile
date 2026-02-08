FROM node:20-alpine

# Install basic packages
RUN apk add --no-cache \
    git \
    bash \
    vim

# Set working directory
WORKDIR /var/www

# Expose Vite port
EXPOSE 5173

# Keep container running and start vite dev server
CMD ["sh", "-c", "if [ -f package.json ]; then npm install && npm run dev; else echo 'Waiting for Laravel project...'; tail -f /dev/null; fi"]
