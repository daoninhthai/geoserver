# ============================================================================
# Multi-stage Dockerfile for Angular + Spring Boot Application
# ============================================================================

# ── Stage 1: Build Angular Frontend ──────────────────────────────────────────
FROM node:18-alpine AS frontend-build

WORKDIR /app/frontend
COPY argon-dashboard-angular-master/package*.json ./
RUN npm ci || npm install
COPY argon-dashboard-angular-master/ ./
RUN npx ng build --configuration=production

# ── Stage 2: Build Spring Boot Backend ───────────────────────────────────────
FROM maven:3.9-eclipse-temurin-17 AS backend-build

WORKDIR /app/backend
COPY asset-management-web-service/pom.xml ./
RUN mvn dependency:go-offline -B
COPY asset-management-web-service/src ./src
RUN mvn clean package -DskipTests

# ── Stage 3: Production Runtime ──────────────────────────────────────────────
FROM eclipse-temurin:17-jre-alpine AS production

LABEL maintainer="developer"
LABEL description="Angular + Spring Boot Asset Management Application"

WORKDIR /app

# Copy backend JAR
COPY --from=backend-build /app/backend/target/*.jar app.jar

# Copy frontend build to static resources
COPY --from=frontend-build /app/frontend/dist/ /app/static/

# Create non-root user
RUN addgroup -S appgroup && adduser -S appuser -G appgroup
USER appuser

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=10s --retries=3 \
  CMD wget --quiet --tries=1 --spider http://localhost:8080/actuator/health || exit 1

ENTRYPOINT ["java", "-jar", "app.jar", "--spring.resources.static-locations=file:/app/static/"]
