import nltk
import string
from nltk.corpus import stopwords
from nltk.tokenize import RegexpTokenizer
import mysql.connector
import sys
# how do we skip these downloads if not needed
# nltk.download('stopwords')
# nltk.download('punkt')

# importing gutenberg files was done with
# rsync -av --del aleph.gutenberg.org::gutenberg

conn = mysql.connector.connect(user="root",
                               password='''enter mysql password''',
                               host="localhost",
                               port=3306,
                               database="gutenberg")

stop_words = set(stopwords.words('english'))
tokenizer = RegexpTokenizer(r'\w+')

book_num = 0
for i in range(1, 26):
    book_num += 1
    print(book_num)
    book_str = str(i)
    book_title = "books/" + book_str + ".txt"
    with open(book_title) as f:
        lines = f.readlines()

    cur = conn.cursor()

    word_pos = 0
    for line in lines:
        tokens = tokenizer.tokenize(line)
        for token in tokens:
            word_pos += 1
            if token not in stop_words:
                # check if token is in database,
                cur.execute("SELECT (id) FROM inverted WHERE word='%s';" %
                            token)
                result = cur.fetchall()
                if result:
                    #if yes: add entry in document
                    term_id = result[0][0]
                    cur.execute(
                        "INSERT INTO document (book_num, pos, term_id) VALUES (%d,%d,%s);"
                        % (book_num, word_pos, term_id))
                else:
                    # if no: create entry in database, also create entry in document
                    cur.execute("INSERT INTO inverted (word) VALUES ('%s');" %
                                token)
                    cur.execute("SELECT (id) FROM inverted WHERE word='%s';" %
                                token)
                    result = cur.fetchall()
                    term_id = result[0][0]
                    cur.execute(
                        "INSERT INTO document (book_num, pos, term_id) VALUES (%d,%d,%s);"
                        % (book_num, word_pos, term_id))

conn.commit()