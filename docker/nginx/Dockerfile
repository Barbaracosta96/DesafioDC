FROM nginx:alpine

# Copy custom nginx config
COPY default.conf /etc/nginx/conf.d/default.conf

# Create directory for Laravel
RUN mkdir -p /var/www/public

# Set working directory
WORKDIR /var/www

# Expose port 80
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
