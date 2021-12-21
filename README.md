# Insert Text

Dieses Addon erweitert den Media Manager um den Effekt `Bild: Text einfügen`.
Der Effekt fügt Text in ein Bild ein ;)

Der Effekt kann genutzt werden um z.B. einen Copyright-Hinweis, Erstellungsdatum, Bild-Titel usw. auf Bildern auszugeben.

**Beispiel:** (Ok, bisserl Overdressed)

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/insert_text/assets/insert_text_demo1.png)

[Photo by Angèle Kamp on Unsplash](https://unsplash.com/@angelekamp) - [Photo](https://unsplash.com/photos/IWzloaVyoOw) (Zitat: Alexander Solschenizyn)

Im Beispiel oben der Effekt mit Font `a song for jennifer`, ein paar extra Leerzeichen im Text, Schriftgröße 90, center, middle, Schriftglättung 1 und Drehung 3 Grad.

> **Hinweis:** Für optimale Ergebnisse und Schonung der Ressourcen bei großen Bildern sollten zuerst die Effekte `Bild: Skalieren`, `Bild: Beschneiden` usw. angewendet werden.

(Markdown mag keine zwei Hinweise hintereinander, darum hier dieser sinnlose Text den Du trotzdem gelesen hast)

> **Noch ein Hinweis:** Ausgabe im Format PNG/WEBP erzielt bessere Ergebnisse als JPG. Effekt `Bild: In JPEG/PNG/GIF/WEBP konvertieren`.

## Effekt-Parameter

Die folgenden Parameter können für den Effekt eingestellt werden.

Die beim Effekt eingestellten Werte gelten als "Default" und können bei Bedarf über die [**Effekt-Parameter in Meta-Daten**](#metaconfig) für jedes Bild individuell geändert werden.

> **Hinweis:** Bei RGBA für die Farben ist nicht CSS-RGBA gemeint, sondern der RGBA-Wert für GD! Siehe https://www.php.net/manual/de/function.imagecolorallocatealpha.php

| Parameter | Beschreibung / Mögliche Werte  |
|---|---|
| **Textausgabe** | Text der auf dem Bild ausgegeben wird. |
| **Textquelle** | Hier kann die Textquelle des Effekts ausgewählt werden. `input` für das Feld `Textausgabe` **oder** ein beliebiges Meta-Feld aus dem Medienpool.<br>Es kann auch eine Textarea aus dem Medienpool ausgewählt werden die noch weitere Möglichkeiten zu diesem Effekt eröffnet (siehe [hier](#metaconfig)) |
| **Schriftgröße** | Ausgabe-Größe für den Text (einfach bisserl rumprobieren!)|
| **Schriftdatei** | Hier kann eine beliebige Font-Datei (.ttf, .otf) aus dem Medienpool ausgewählt werden |
| **Schriftfarbe** | Farbe für die Text-Ausgabe<br>Die Schriftfarbe kann in zwei verschiedenen Formaten angegeben werden:<br>1) Hex-Farbwert ohne Alpha-Transzparenz z.B. `#fff` oder `#ffffff` <br>2) RGBA-Wert (GD) mit Alpha-Transparenz z.B. `255,255,255,66` |
| **Horizontale Ausrichtung** | Horizontale Ausrichtung des Textes.<br>Mögliche Werte: `left` `center` `right` |
| **Vertikale Ausrichtung** | Vertikale Ausrichtung des Textes.<br>Mögliche Werte: `top` `middle` `bottom` |
| **Horizontaler Abstand zum Rand** | Horizontaler Abstand des Textes zum Rand (es sind auch negative Werte möglich) |
| **Vertikaler Abstand zum Rand** | Vertikaler Abstand des Textes zum Rand (es sind auch negative Werte möglich) |
| **Schriftglättung** | Schriftglättung für die Textausgabe<br>Mögliche Werte: `0` bis `5`<br>`0` = ohne Schriftglättung (z.B. für Pixelfonts)<br>`1` = Standard (normalerweise ausreichend)<br>**Achtung:** ein Wert größer 1 benötigt natürlich mehr Ressourcen! |
| **Farbe Text-Schatten** | Der Text kann auch mit einem Schatten versehen werden<br>Die Farbe für den Schatten kann in zwei verschiedenen Formaten angegeben werden:<br>1) Hex-Farbwert ohne Alpha-Transzparenz z.B. `#fff` oder `#ffffff` <br>2) RGBA-Wert (GD) mit Alpha-Transparenz z.B. `255,255,255,66` |
| **Farbe Text-Hintergrund** | Text mit einer Hintergrundfarbe unterlegen<br>Die Hintergrundfarbe kann in zwei verschiedenen Formaten angegeben werden:<br>1) Hex-Farbwert ohne Alpha-Transzparenz z.B. `#fff` oder `#ffffff` <br>2) RGBA-Wert (GD) mit Alpha-Transparenz z.B. `255,255,255,66` |
| **Padding Text-Hintergrund** | Hier kann der Seitenabstand der Schrift zum farbigen Hintergrund festgelegt werden<br>Mögliche Werte: z.B. `10` (auch hier bei Bedarf bisserl probieren)|
| **Textdrehung** | Der Text kann auch "gedreht" ausgegeben werden.<br>Mögliche Werte: `0` bis `360` (nachdenken!)<br>positiver Wert: Drehung nach Links<br>negativer Wert: Drehung nach Rechts |

## Screenshot Effekt-Parameter

Hier die Parameter die bei einfügen des Effekts vorgeblendet werden.
Ohne Änderung der Parameter wird der Text oben mittig mit einem Abstand von 30 Pixeln in schwarz ausgegeben.

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/insert_text/assets/insert_text-effekt-configuration.png)

<a name="metaconfig"></a>

## Effekt-Parameter in Meta-Daten

Die in dem Effekt eingestellten Werte gelten als "Default" für alle Bilder auf die dieser Medientyp angewendet wird.

Wenn eine "Textarea" aus den Meta-Daten als Textquelle ausgewählt wurde, kann für jedes Bild eine individuelle Einstellung zum Effekt angewendet werden.

Dafür muss in der **1.** Zeile der Textarea eine Zeile mit den Parametern eingefügt werden.
Trennzeichen der Parameter ist `|`, bekannt aus `yform`.

### Format der Parameter im Meta-Feld

Die Erste Zeile in der Textarea in folgendem Format

```
0        1        2     3    4    5          6         7         8           9       10        11
fonzsize|font.ttf|color|hpos|vpos|offsetleft|offsettop|antialias|shadowcolor|bgcolor|bgpadding|angle
```

Werte die in dieser Zeile nicht gesetzt werden, werden aus dem definierten Effekt im Medientyp übernommen.

Werte die hier gesetzt werden überschreiben den Wert aus dem definierten Effekt im Medientyp.

**Beispiel:**

Ein Meta Textarea-Feld mit individuellen Einstellungen zu diesem einen Bild im Medienpool.

```
90|a_song_for_jennifer.ttf|#3e3e3e|center|middle|0|0|1||||3
Die Lösung
ist immer
einfach.

Man muss
  sie nur
    finden.
```

## Noch Fragen oder Anregungen? Einen Bug gefunden?

Dann geht es hier weiter ...

* Auf Github: [https://github.com/FriendsOfREDAXO/insert_text](https://github.com/FriendsOfREDAXO/insert_text)

* im Slack-Channel: [https://friendsofredaxo.slack.com/](https://friendsofredaxo.slack.com/)

Hier noch ein Beispiel mit der **mehrfachen** Anwendung des Effekts: (Spielerei wenn man Zeit hat oder nicht schlafen kann)

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/insert_text/assets/insert_text_demo2.png)

## Credits

* [Friends Of REDAXO](https://github.com/FriendsOfREDAXO)

* [Michael Ziem](https://github.com/mizmiz) (Project Lead)

* [Andreas Eberhard](https://github.com/aeberhard)
