|               |               |               |               |               |               |
|:-------------:|:-------------:|:-------------:|-------------:|-------------:|-------------:|
| ![Hack the planet](https://img.shields.io/badge/Hack-The%20Planet-orange) | [![Discord](https://img.shields.io/discord/667340023829626920?logo=discord)](https://discord.gg/ahVq54p) | [![Twitter](https://img.shields.io/twitter/follow/4xsample?style=social&logo=twitter)](https://twitter.com/4xsample/follow?screen_name=shields_io) | [![4Xsample@mastodon.social](https://img.shields.io/badge/Mastodon-@4Xsample-blueviolet?style=for-the-badge&logo=mastodon)](https://mastodon.social/@4Xsample) | [![4Xsample](https://img.shields.io/badge/Twitch-4Xsample-6441A4?style=for-the-badge&logo=twitch)](https://twitch.tv/4Xsample) | [![PayPal](https://img.shields.io/badge/PayPal-00457C?style=for-the-badge&logo=paypal&logoColor=white)](https://www.paypal.com/donate/?hosted_button_id=EFVMSRHVBNJP4) |

# Integració de Tautulli amb [Plex](https://plex.tv) en WordPress

Aquest projecte et permet mostrar dades del teu servidor de [Plex](https://plex.tv) a WordPress sense haver de donar accés al servidor directament. Utilitza l'[API de Tautulli](https://github.com/Tautulli/Tautulli/) per obtenir les dades en temps real i exposar-les mitjançant una interfície visual i personalitzable a WordPress. A continuació t’explico com fer-ho, què necessites i altres detalls importants per la integració.

## Requisits

- **Servidor [Plex](https://plex.tv)**: Cal un servidor [Plex](https://plex.tv) connectat amb [Tautulli](https://tautulli.com). Això et permetrà tenir les estadístiques del contingut de [Plex](https://plex.tv) centralitzades a [Tautulli](https://tautulli.com).
- **[Tautulli](https://github.com/Tautulli/Tautulli/) configurat amb API**: És necessari que [Tautulli](https://github.com/Tautulli/Tautulli/) estigui configurat correctament per exposar l’API que utilitzarem.
- **Plugin PHPCode Snippets a WordPress**: Utilitzarem el plugin [PHPCode Snippets](https://wordpress.org/plugins/phpcode-snippets/) per inserir codi PHP personalitzat a la nostra web de WordPress, que ens permetrà fer crides a l’API de [Tautulli](https://tautulli.com) des de la web.

## Instal·lació

1. **Configura [Tautulli](https://tautulli.com) amb el servidor [Plex](https://plex.tv)**: No és l’objectiu d’aquest projecte, així que no entrem en detall sobre com connectar-los. Això sí, assegura't que [Tautulli](https://github.com/Tautulli/Tautulli/) estigui rebent les dades del teu [Plex](https://plex.tv) per poder consultar l’API.
   
2. **Instal·la el plugin PHPCode Snippets a WordPress**: Ves a "Plugins" a WordPress, cerca "PHPCode Snippets" i instal·la’l. Amb això, podràs inserir el codi directament en qualsevol pàgina o entrada de WordPress sense complicacions.

3. **Crea un nou Snippet a PHPCode Snippets**: Afegeix el codi PHP que trobaràs a `tautulli_plates_for_wordpress.php` com un nou snippet al plugin, substituint la clau de l’API de [Tautulli](https://github.com/Tautulli/Tautulli/) amb la teva pròpia. 

4. **Inclou l’Snippet a les pàgines que vulguis**: Fes servir el codi curt proporcionat per PHPCode Snippets per inserir el contingut allà on vulguis a la teva web. 

## Ús

Aquest projecte exposa una interfície visual de les teves biblioteques de [Plex](https://plex.tv) (pel·lícules, sèries i música) mitjançant l’API de [Tautulli](https://tautulli.com). Cada biblioteca es mostra amb una imatge de fons relacionada amb l’últim contingut afegit. Pots veure un exemple de com queda aquest sistema en acció a la [meva web](https://www.4xsample.cat/coses-de-plex/).

## Com funciona

- **[Plex](https://plex.tv)** envia les dades a **[Tautulli](https://tautulli.com)**, que centralitza les estadístiques d’ús i contingut.
- Amb l'API de **[Tautulli](https://github.com/Tautulli/Tautulli/)**, obtenim aquestes dades per mostrar-les a WordPress, sense necessitat de donar accés al servidor [Plex](https://plex.tv) directament.
- Això fa que puguis mostrar contingut de manera segura, només exposant la resposta de l'API i no les credencials o informació confidencial.

## Personalització

L’estructura CSS i HTML està inclosa en el codi PHP per facilitar la integració. Si vols, pots modificar els estils per adaptar la presentació visual a la teva web. Amb una mica de coneixement de CSS, pots canviar colors, mides i estils de fonts per adaptar el disseny al teu gust.

## Exemple en Acció

Si vols veure com queda, visita [4Xsample - Coses de Plex](https://www.4xsample.cat/coses-de-plex/) per veure l’aspecte final d’aquest projecte amb dades reals d’un servidor [Plex](https://plex.tv).

## Disclaimer: 
*Aquest codi s'ofereix tal com és i no es garanteix que funcioni correctament en totes les condicions. No em faig responsable dels danys que puguin resultar de l'ús d'aquesta informació. Utilitzeu-lo sota la vostra pròpia responsabilitat. Si teniu dubtes pregunteu i respondré al que pugui. Si voleu obrir proposar canvis podeu obrir fork i i voleu seguir-me, al panel del principi d'aquest readme podeu trobar links a les meves xarxes socials, Twitch i PayPal per si també voleu donar suport al meu treball.*
