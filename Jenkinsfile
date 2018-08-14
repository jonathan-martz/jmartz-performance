String cron_string = BRANCH_NAME == "master" ? "0 18 * * *" : "0 18 * * *"

pipeline {
    agent any

    triggers { cron(cron_string) }

    stages {
        stage('Jenkins') {
                steps {
                    sh 'robo execute jmartz-jenkins'
                }
            }
    }
}
