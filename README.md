# 60-LED-Build-Light-Board
Shows the status of 10 Jenkins jobs with up to 6 previous statuses for each job on a 60 LED strip.  Look at the image below and see the Legend in the picture to see how each LED represents the status.

### Background:
I wanted to want to be able to display Jenkins job statuses on a physical board...so I built this using help from an adafruit tutorial, adatruit products and some open source libraries. Here it is:
![picture alt](https://github.com/bjanosngt/60-LED-Build-Light-Board/blob/master/60LedCorkboardBuildStatus.JPG)

### Hardware:
Product  | Link
------------- | -------------
Raspberry Pi Model B+ V1.2  | 
Bread board  |
Corkboard  |
Jumper wires | https://www.adafruit.com/products/368?q=jumper%20wire&
Adafruit NeoPixel Digital RGB LED Strip - White 60 LED - WHITE  | https://www.adafruit.com/product/1138
74AHCT125 - Quad Level-Shifter (3V to 5V)  | https://www.adafruit.com/products/1787
5V 10A switching power supply  | https://www.adafruit.com/products/658
Female DC Power adapter - 2.1mm jack to screw terminal block  | https://www.adafruit.com/products/368

### Usage:
* Follow the following tutorial to hook up the Raspberry Pi, bread board and LED strip
 * https://learn.adafruit.com/neopixels-on-raspberry-pi/wiring
* Compile and install the rpi_ws281x Library using the same tutorial
 * https://learn.adafruit.com/neopixels-on-raspberry-pi/software
 * Get the strandtest.py example working correctly.  If it doesn't work correctly then keep reading.  I had to do the following in order to get mine working correctly.  Just out of the box, when running strandtest.py it would sort of randomly display colors all down the strip.  Apparently the built-in audio hardware uses the same GPIO 18 to drive audio output and the sound drivers can be more aggressive in taking away control of GPIO 18 from any other process.  Create a kernel module blacklist file that prevents all the sound drivers from loading:
   * sudo vi /etc/modprobe.d/blacklist-rgb-matrix.conf
    * add the following lines:
      * `blacklist snd_bcm2835`
      * `blacklist snd_pcm`
      * `blacklist snd_timer`
      * `blacklist snd_pcsp`
      * `blacklist snd`
    * Save the file and quit vi
    * sudo update-initramfs -u
    * reboot and confirm no "snd" modules are running by executing the command "lsmod"
    * Now rerun the strandtest.py and it should work just fine.
 * Grab the showBuild.py and status.php from above and put them wherever you want.
 * In showBuild.py you'll want to do the following:
    * Update any paths to match yours
 * In status.php you'll want to do the following:
    * Get the httpful PHP library (to do HTTP GET requests) here - http://phphttpclient.com/
    * Update any paths to match yours
    * Insert the Jenkins URLs (10 total) that you want show.
    * Fix the HTTP GET username and passwords if you require basic auth.  If not, remove the authentication mechanism.
  * Run it:
    * `php status.php`
    * Or set it to run automatically by cron:
      * `# Show the build status during business hours only: 8am - 5:59pm (MST) M-F`
      * `* 15,16,17,18,19,20,21,22,23,0 * * 1,2,3,4,5 /usr/bin/php /home/pi/BuildLight/status.php`
  * Cut it up!
    * In order to make the 6 LED segments more seperated I cut (like butter with scissors) the strip into 10 pieces with 6 LEDs on each.  There are cut lines on the strip that go right through the GND, Din and 5V contacts.  Then you can just solder wires between the 3 contacts to "extend" the strip.  Let me know if you want a picture of this.
  * Mount it on your favorite board (or in my case the only one you can find at your local hardware store).
