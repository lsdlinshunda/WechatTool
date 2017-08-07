import sys
import cv2
import aircv as ac

def main():
    imsrc = ac.imread(sys.argv[1])
    imobj = ac.imread(sys.argv[2])
    pos = ac.find_template(imsrc, imobj)
    print (pos['result'])

if __name__ == "__main__":
    main()
