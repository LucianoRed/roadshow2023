#!/bin/bash
ansible-rulebook -i /tmp/inventario.ini -r /tmp/kafka.yaml --verbose --controller-url $CONTROLLER --controller-token $TOKEN --env-vars BROKER,BROKER_PORT,BROKER_TOPIC,JOB_TEMPLATE