# 60-LED-Build-Light-Board
Shows the status of 10 Jenkins jobs with up to 6 previous statuses for each job on a 60 LED strip.

### Background:
I wanted to want to be able to display Jenkins job statuses on a physical board...so I built this using help from an adafruit tutorial, adatruit products and some open source libraries. (See the 60LedCorkboardBuildStatus.JPG to see the final product)

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
 * In showBuild.py you'll want to fix any paths to match yours and make sure it all looks good.
 * In status.php you'll want to update the following:
   * Update any paths to match yours
   * Insert the Jenkins URLs (10 total) that you want show.
   * Fix the HTTP GET username and passwords if you require basic auth.  If not, remove the authentication mechanism.
