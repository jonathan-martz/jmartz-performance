
pipeline {
    agent any

    stages {
        stage('jmartz.de') {
            steps {
                sh 'robo execute jmartz.de'
            }
        }
        stage('copy reports') {
            steps {
                sh 'robo copy'
            }
        }
    }
}
