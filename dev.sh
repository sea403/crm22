#!/bin/bash

# dev.sh - Start Laravel backend and frontend dev servers

# Start Laravel backend
echo "Starting Laravel backend server..."
(cd backend && php artisan serve) &

# Start frontend dev server (Vite, Webpack, etc.)
echo "Starting frontend dev server..."
(cd frontend && npm run dev)

# Wait for background jobs
wait
