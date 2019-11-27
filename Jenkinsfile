
pipeline {
    agent any

    stages {
        stage('Download config') {
            steps {
                sh 'robo download:config'
            }
        }
        stage('Install Dependencies') {
            steps {
                sh 'robo composer:install'
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
