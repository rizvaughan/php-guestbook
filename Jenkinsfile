pipeline {
    agent any
    
    environment {
        AWS_ACCOUNT = 'xxxxxxxx'  // Will be replaced
        AWS_REGION = 'eu-west-1'
        PHP_REPO = 'php-guestbook'
        MYSQL_REPO = 'php-guestbook-mysql'
        IMAGE_TAG = "${env.BUILD_NUMBER}-${env.GIT_COMMIT[0..7]}"
    }
    
    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', 
                    url: 'https://github.com/rizvaughan/php-guestbook.git'
            }
        }
        
        stage('Validate PHP Syntax') {
            steps {
                sh '''
                    echo "Validating PHP syntax..."
                    find app/ -name "*.php" -exec php -l {} \\;
                    echo "✅ PHP syntax OK!"
                '''
            }
        }
        
        // ===== NEW: Build MySQL Image =====
        stage('Build MySQL Image') {
            steps {
                sh '''
                    echo "Building MySQL image..."
                    docker build -t ${MYSQL_REPO}:${IMAGE_TAG} mysql/
                    docker tag ${MYSQL_REPO}:${IMAGE_TAG} ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${MYSQL_REPO}:${IMAGE_TAG}
                    docker tag ${MYSQL_REPO}:${IMAGE_TAG} ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${MYSQL_REPO}:latest
                    echo "✅ MySQL image built!"
                '''
            }
        }
        
        // ===== Build PHP Image =====
        stage('Build PHP Image') {
            steps {
                sh '''
                    echo "Building PHP image..."
                    docker build -t ${PHP_REPO}:${IMAGE_TAG} .
                    docker tag ${PHP_REPO}:${IMAGE_TAG} ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${PHP_REPO}:${IMAGE_TAG}
                    docker tag ${PHP_REPO}:${IMAGE_TAG} ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${PHP_REPO}:latest
                    echo "✅ PHP image built!"
                '''
            }
        }
        
        // ===== Push BOTH images =====
        stage('Push to ECR') {
            steps {
                withAWS(credentials: 'aws', region: 'eu-west-1') {
                    sh '''
                        # Login to ECR
                        aws ecr get-login-password --region ${AWS_REGION} | \
                            docker login --username AWS --password-stdin ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com
                        
                        # Push MySQL
                        echo "Pushing MySQL image..."
                        docker push ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${MYSQL_REPO}:${IMAGE_TAG}
                        docker push ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${MYSQL_REPO}:latest
                        
                        # Push PHP
                        echo "Pushing PHP image..."
                        docker push ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${PHP_REPO}:${IMAGE_TAG}
                        docker push ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${PHP_REPO}:latest
                        
                        echo "✅ All images pushed to ECR!"
                    '''
                }
            }
        }
    }
    
    post {
        success {
            echo "✅ Build successful! All images pushed to ECR."
        }
        failure {
            echo "❌ Build failed! Check logs."
        }
    }
}
