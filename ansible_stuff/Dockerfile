FROM docker.io/lasher/ansible-eda
COPY donothing.sh /tmp/donothing.sh
COPY ansible-eda.sh /tmp/ansible-eda.sh
COPY inventario.ini /tmp/inventario.ini
COPY kafka.yaml /tmp/kafka.yaml
CMD ["/tmp/ansible-eda.sh"]