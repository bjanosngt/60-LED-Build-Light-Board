# NeoPixel build board control
# Author Ben Janos (bjanos@gmail.com)
#
# Uses the rpi_ws281x library to control the LEDs
# Get it here - https://github.com/jgarff/rpi_ws281x
#
# This program assumes you have a file (status.txt) that contains
# the color of each pixel row by row.  6 colors in each row with
# 10 rows total.  Acceptable colors are red, green, yellow, blue and off
#
# Example status.txt file:
# green green green green green green
# green green green green green green
# green green green green green green
# green green green green green green
# red red red red red red
# green green green green green green
# green green green green green green
# off off red red green green
# green green green green green green
# yellow yellow yellow yellow yellow yellow 

import time
import sys
from neopixel import * 

# LED strip configuration
LED_COUNT      = 60      # Number of LED pixels.
LED_PIN        = 18      # GPIO pin connected to the pixels (must support PWM!).
LED_FREQ_HZ    = 800000  # LED signal frequency in hertz (usually 800khz)
LED_DMA        = 5       # DMA channel to use for generating signal (try 5)
LED_BRIGHTNESS = 30      # Set to 0 for darkest and 255 for brightest^M
LED_INVERT     = False   # True to invert the signal (when using NPN transistor level shift)

# Looks like the rpi_ws281x library have RED and GREEN switched?!?
RED = Color(0, 255, 0)
GREEN = Color(255, 0, 0)
YELLOW = Color(255, 255, 0);
BLUE = Color(0, 0, 255);
OFF = Color(0, 0, 0);

def setPixel(color, pixel):
    if color == 'red':
        strip.setPixelColor(pixel, RED)
    elif color == 'green':
		strip.setPixelColor(pixel, GREEN)
    elif color == 'yellow':
			strip.setPixelColor(pixel, YELLOW)
    elif color == 'blue':
		strip.setPixelColor(pixel, BLUE)
    elif color == 'off':
		strip.setPixelColor(pixel, OFF)

strip = Adafruit_NeoPixel(LED_COUNT, LED_PIN, LED_FREQ_HZ, LED_DMA, LED_INVERT, LED_BRIGHTNESS)
strip.begin()

i = 0;

with open('/home/pi/BuildLight/status.txt') as f:
    for line in f:
		print line
		lineStatus = line.split()
		print len(lineStatus)
		if i != 0:
			i += 1
		if 0 < len(lineStatus):
			setPixel(lineStatus[0], i)
			i += 1
		else:
			setPixel('off', i)
		if 1 < len(lineStatus):
			setPixel(lineStatus[1], i)
			i += 1
		else:
			setPixel('off', i)
		if 2 < len(lineStatus):
			setPixel(lineStatus[2], i)
			i += 1
		else:
			setPixel('off', i)
		if 3 < len(lineStatus):
			setPixel(lineStatus[3], i)
			i += 1
		else:
			setPixel('off', i)
		if 4 < len(lineStatus):
			setPixel(lineStatus[4], i)
			i += 1
		else:
			setPixel('off', i)
		if 5 < len(lineStatus):
			setPixel(lineStatus[5], i)
		
# After setting all 60 LED pixels show the entire strip.
strip.show()