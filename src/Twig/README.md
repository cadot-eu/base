
# basetwig

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=cadot-eu_twigbundle&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=cadot-eu_twigbundle)

## implementation de functions php

| twig function | description |
| _________ | _______ |
| TBdd | return die and dump of symfony |
| TBgetenv |  return variable from $\_ENV |

## functions d'affichage

| twig function     | description                                       |
| ----------------- | ------------------------------------------------- |
| TBdatefr          | return a date in french format                    |
| TBgetPublic       | clean for return public file path                 |
| TBgetFilename     | return filename                                   |
| TBimgToBase64     | return a immage code in base64, inline option     |
| TBjsondecode      | json decode                                       |
| TBfaker           | make french faker                                 |
| TBfakeren         | make english faker                                |
| TBfakericon       | faker of bootstrap icons                          |
| TBsanitize        | string clean, without space, character...         |
| TBobjetProperties | array of properties object                        |
| TBtxtfromhtml     | decoded and cleaned                               |
| TBJsonPretty      | return a pretty string from json                  |
| TBuploadmax       | return the lowest file upload from php and server |
