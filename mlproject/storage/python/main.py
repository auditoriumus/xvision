import cv2.cv2
from matplotlib import pyplot as plt
import numpy as np
import imutils
import easyocr
import uuid
from sys import argv

try:
    filepath = argv[1]
    img = cv2.imread(filepath)  # чтение изображения
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)  # преобраования в серый вид
    bfilter = cv2.bilateralFilter(gray, 11, 17, 17)  # убираем шум
    edged = cv2.Canny(bfilter, 30, 200)  # находим края всех контуров
    keypoints = cv2.findContours(edged.copy(), cv2.RETR_TREE, cv2.CHAIN_APPROX_SIMPLE)  # находим сами контуры
    contours = imutils.grab_contours(keypoints)  # получаем контуры
    contours = sorted(contours, key=cv2.contourArea, reverse=True)[:10] # берем последние замкнутые квадратные контуры
    location = None
    # проходим по циклу и при обнаружении замкнутого контура, состоящих из 4 точек, выходим из цикла, записывая координаты точек в location
    for contour in contours:
        approx = cv2.approxPolyDP(contour, 10, True)
        if len(approx) == 4:
            location = approx
            break

    mask = np.zeros(gray.shape, np.uint8)
    new_image = cv2.drawContours(mask, [location], 0, 255, -1)
    new_image = cv2.bitwise_and(img, img, mask=mask)
    (x, y) = np.where(mask == 255)
    (x1, y1) = (np.min(x), np.min(y))
    (x2, y2) = (np.max(x), np.max(y))
    cropped_image = gray[x1:x2 + 1, y1:y2 + 1]

    reader = easyocr.Reader(['en'])
    result = reader.readtext(cropped_image)
    text = result[0][-2]
    #print(text)
    font = cv2.FONT_HERSHEY_COMPLEX
    res = cv2.putText(img, text=text, org=(approx[0][0][0], approx[1][0][1] + 60), fontFace=font, fontScale=1,
                      color=(0, 255, 0), thickness=2, lineType=cv2.LINE_AA)
    res = cv2.rectangle(img, tuple(approx[0][0]), tuple(approx[2][0]), (0, 255, 0), 3)
    name = '/var/www/html/storage/processed/images/' + str(uuid.uuid1()) + '.png'
    plt.savefig(name)

    print(text + "\n")
    # print(name)
    # plt.imshow(cv2.cvtColor(res, cv2.COLOR_BGR2RGB))
    # plt.show()
except Exception as e:
    print (str(e))
