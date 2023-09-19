# roadshow2023
Tech Demo for Hyperautomation

## Requirements
- Openshift with 3 nodes

## Deploy App
Deploy using s2i https://github.com/LucianoRed/roadshow2023.git and use the path /uploadapp for the app

Colocar as variaveis de ambiente:
- AWS_KEY
- AWS_SECRET
- KAFKA_BROKERS
- KAFKA_TOPIC

## Deploy Kafka
In Openshift Create a Kafka cluster using Operator. Create a topic to store images metadata


## Deploy Ansible Controller
In ansible controller, add the project https://github.com/LucianoRed/roadshow2023.git and create a job template for the action

## Deploy Ansible EDA Image
In openshift, create an project and deploy the image docker.io/lasher/ansible-eda with the following env variables
- BROKER - The address of the kafka server
- BROKER_PORT - The port of the kafka broker
- BROKER_TOPIC - The name of the topic to get messages
- JOB_TEMPLATE - The name of the job templte on ansible controller
- CONTROLLER - The address of ansible controller
- TOKEN - The token generated on controller to execute