
pipeline {
    agent any

    stages {
        stage('Load config') {
                steps {
                    sh 'robo load:config'
                }
            }
        stage('Lighthouse') {
            steps {
                sh 'robo execute'
            }
        }
        stage('copy reports') {
            steps {
                sh 'robo copy'
            }
        }
    }
}
