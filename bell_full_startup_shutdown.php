<?php


BELL FULL POWER DOWN
    [sim]
        Control Loading = Off
        Vibration platform = Off

    [Main Host]
        sas@bell412> unload
        root# shutdown -F

        Power off the server (switch)

    --Visual system--

        [Visual Host]
            Q   (to quit RTS application)

        [RACU box]
            Connect to the socket in sim
            Turn off raster and DOT for all channels (R G B) in all projectors & Monitors. P1,P2,P3,P4,P5,M6,M7
            
        [Mac]
            Power off the Mac (power button on back)
            
        [ESU] 
            Main power = Off (switch)   (2 Bigger white racks) 
            V1 = Off (switch at bottom of rack)
            V4 = Off (switch at bottom of rack)

        [Visual Host]
            >su         (password: relic)
            root# init 0   

            Power off the indigo server/host


        [P1]
            Sequence stop ( Red? Button )
            P1 power cabinett = off 
            C/B 101 = off 
            C/B 102 = off 

        [Wall mounted Circuit Breakers]
            S1 = Off (Left middle panel)
            P1 = Off (Left middle panel)
            Middle switches (Top right panel) read instructions on wall

        All systems (DN1 also) should be powered down now, and the emergency lighting in sim ON. 




BELL FULL POWER UP

    [Wall mounted Circuit Breakers]
            S1 = On (Left middle panel)
            P1 = On (Left middle panel)
            Middle switches = On (Top right panel) read instructions on wall


    [P1]
        C/B 101 = on 
        C/B 102 = on 
        P1 power cabinett = on 
        Sequence start ( Green Button )
            Touch Screen boots up.. 
            select continue
            Progress bar loads to half way to  "holding point" (while waiting for main host to start)
        
    [Sim access door A817] (leftside under sim)
        Switch/check on PSU is on

    [DN1]
        Power/ready (press button to) start
        Set pumps in MANUAL mode
        Start each pump (one at a time)

    [Main Host]
        Power on
        login: sas

    [Sim]
        Start the IOS thin-clients (power switch under table)

    [Visual Host]
        Power on host (Indigo computer)
        Login: simex/sex
        

    [ESU]
        Main power = On (switch)   (2 Bigger white racks) 
            
    [V1 rack]  
        Power On (switch at bottom of rack)
    
    [V4 rack]  
        Power On

    [V1]
        Start sequence (Button with lid at top of rack)
    
    [V4]
        Start sequence (Button with lid at top of rack)
    
    [Mac]
        Power up (switch on back)

    [RACU]
        Display will start loading progress bars and boot up
        Select: Sysconfig (password: 3608)

        Turn on RAST & DOT for all projectors (and both monitors) P1-P6, M7,M8
        Download (active system) to all projectors/monitors
            They should start projecting picture after a few minutes.
    
    [Visual Host]
        >load (takes a while to load)
        >run  (starts RTC)  

    [Sim access door A805] (frontside under sim)
        Start vacum pump for mirror

    [Main Host]
        >load412  (or which ever hardware config is installed in the sim currently)

    [sim]
        Control Loading = On
        Vibration = On

    Done!

--------------


Tips: Visual system problems
    If a projector is RED, try to restart it by connecting the [RACU] box and Download the "active system" to the faulty projector. 
    It should start showing picture after a few minutes. 

    Select "Read head parameters" to see status the projectors:
    Check for high voltage 900v and Low voltage 

    If the projectors still wont start (red light) then:
        Turn Off RGB DOT & RASTAR with RACU BEFORE climbing up to projectors to power cycle them. 
        Important: START AND END the power CYCLE sequence with the HIGH VOLTAGE switch, 
        (so that high voltage switch is never on without low voltage switch on. 


    
IMPORTANT NOTE: !! Allways turn OFF Rastar & DOT(RGB) with [RACU] remote box BEFORE turning off power switches on projectors, else damage may occur !!

EOF
