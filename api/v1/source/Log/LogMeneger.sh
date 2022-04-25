
# Limpa todos os logs deixando apenas as ultimas 150 linhas
# processo rodando no CRON de 1 em 1 minuto

declare -a LOGS=(
    "/var/www/origin/curlrequest/tcc/api/v1/source/Log/api_authorization.log" 
    "/var/www/origin/curlrequest/tcc/api/v1/source/Log/api_critical.log" 
    "/var/www/origin/curlrequest/tcc/api/v1/source/Log/api_debug.log"
    )

lines=$(ps aux | grep LogMeneger.sh | grep -v grep | wc -l)

if [ $lines -eq  0 ]; then 
    while true; do
        for LOG in "${LOGS[@]}"; do
            nLines=$(cat $LOG | wc -l)   
            line=$(($nLines-150))
            if [ $line -gt 0 ]; then
                sed -i "1,${line}d" $LOG  

            fi
        done        
        sleep 1

    done

fi