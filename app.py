from flask import Flask, request, jsonify, make_response
import pymysql
import socket

# Setup Awal untuk Mendapatkan IP yang digunakan
app = Flask(__name__)
host_name = socket.gethostname()
host_ip = socket.gethostbyname(host_name)

@app.route('/')
@app.route('/index')
def index():
    return "Hello, World!"

# Menghubungkan ke Database 
mydb = pymysql.connect(
    host="localhost",
    user="root",
    password="123321",
    database="db_webservice"
)

# Routing dan Method untuk Update Perintah LED (on/off) untuk Database dari Website
@app.route('/web_command', methods=['POST'])
def web_command():
    hasil = {"status": "gagal"}
    query = "UPDATE tb_command SET command = %s WHERE id = 1"
    try:
        data = request.json
        command = data["command"]
        mycursor = mydb.cursor()
        mycursor.execute(query, command)
        mydb.commit()
        hasil = {"status" : "berhasil"}
    except Exception as e:
        print("Error : " + str(e))
    
    return jsonify(hasil)

# Routing dan Method untuk Mendapatkan Nilai Sensor LDR dari Database untuk Website
@app.route('/web_sensor', methods=['GET'])
def web_sensor():
    query = "SELECT * FROM tb_sensor ORDER BY id DESC LIMIT 1"

    mycursor = mydb.cursor()
    mycursor.execute(query)
    row_headers = [x[0] for x in mycursor.description]
    data = mycursor.fetchall()
    json_data = []
    for result in data:
        json_data.append(dict(zip(row_headers, result)))
    mydb.commit()
    return make_response(jsonify(json_data),200)

# Routing dan Method untuk Mendapatkan Perintah LED (on/off) dari Database untuk ESP32
@app.route('/esp_command', methods=['GET'])
def esp_command():
    query = "SELECT * FROM tb_command ORDER BY id DESC LIMIT 1"

    mycursor = mydb.cursor()
    mycursor.execute(query)
    row_headers = [x[0] for x in mycursor.description]
    data = mycursor.fetchall()
    json_data = []
    for result in data:
        json_data.append(dict(zip(row_headers, result)))
    mydb.commit()

    return make_response(jsonify(json_data),200)

# Routing dan Method untuk Update Nilai Sensor LDR dari ESP32 untuk Database
@app.route('/esp_sensor', methods=['POST'])
def esp_sensor():
    hasil = {"status" : "gagal"}
    query = "UPDATE tb_sensor SET nilai = %s WHERE id = 1"
    try:
        data = request.json
        nilai = data["nilai"]
        mycursor = mydb.cursor()
        mycursor.execute(query, nilai)
        mydb.commit()
        hasil = {"status" : "berhasil"}
    except Exception as e:
        print("Error : " + str(e))
    
    return jsonify(hasil)

# Menggunakan IP Default dan Port 5010
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5010)