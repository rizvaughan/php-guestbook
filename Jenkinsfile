pipeline {
    agent any
    
    environment {
        AWS_ACCOUNT = '471112924304'  // CHANGE THIS!
        AWS_REGION = 'eu-west-1'
        ECR_REPO = 'php-guestbook'
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
        
        stage('Build Docker Image') {
            steps {
                sh '''
                    docker build -t ${ECR_REPO}:${IMAGE_TAG} .
                    docker tag ${ECR_REPO}:${IMAGE_TAG} ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${ECR_REPO}:${IMAGE_TAG}
                    docker tag ${ECR_REPO}:${IMAGE_TAG} ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${ECR_REPO}:latest
                '''
            }
        }
        
        stage('Push to ECR') {
            steps {
                withAWS(credentials: 'aws', region: 'eu-west-1') {
                    sh '''
                        aws ecr get-login-password --region ${AWS_REGION} | \
                            docker login --username AWS --password-stdin ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com
                        docker push ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${ECR_REPO}:${IMAGE_TAG}
                        docker push ${AWS_ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${ECR_REPO}:latest
                    '''
                }
            }
        }
    }
    
    post {
        success {
            echo "✅ Build successful! Image pushed to ECR."
        }
        failure {
            echo "❌ Build failed! Check logs."
        }
    }
}
