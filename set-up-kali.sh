#!/bin/bash

sudo apt-get update
sudo apt install -y build-essential autoconf libssl-dev libyaml-dev zlib1g-dev libffi-dev libgmp-dev rustc docker.io

mise config experimental true
