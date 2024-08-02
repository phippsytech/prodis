# Use a small, lightweight base image
FROM nginx:alpine

# Copy the HTML file into the nginx html directory
COPY ./html/index.html /usr/share/nginx/html/index.html

# Expose port 80 to the outside world
EXPOSE 80
