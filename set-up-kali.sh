#!/bin/bash

sudo apt-get update
sudo apt install -y build-essential autoconf libssl-dev libyaml-dev zlib1g-dev libffi-dev libgmp-dev rustc docker.io

sudo gpasswd -a $USER docker

mise config experimental true
