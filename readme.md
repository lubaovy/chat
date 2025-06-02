docker build -t api_web_leech_truyen_audio .

docker run -d --restart always -v /root/dir_api_web_leech_truyen_audio:/_app_/utils/download --name api_web_leech_truyen_audio -p 60074:60074 api_web_leech_truyen_audio

docker save -o api_web_leech_truyen_audio.tar api_web_leech_truyen_audio

docker load -i api_web_leech_truyen_audio.tar


docker exec -it api_web_leech_truyen_audio bash

docker build -t sys_55013 .  
docker run -d -p 55013:55013 --name sys_55013 sys_55013  
docker stop sys_55013
docker rm sys_55013
docker run -d -p 55013:55013 --name sys_55013 --env-file .env sys_55013     
docker logs sys_55013 
docker ps -s