### Notes

* There is a field called `compliments` in the users file I didn't convert it, because I didn't understand it. 
* I didn't convert the reviews files because it's huge, let me know if you need it.


### MySQL Installation 

There is a tool called XAMPP that you can use in order to have `MySQL` and `phpMyAdmin` which will give you a GUI to the mysql instead of using the command line.

##### Download For Windows and Mac
http://www.apachefriends.org/download.html

After you install the XAMPP you can run the apache server and MySQL server in your computer and then visit http://localhost and http://localhost/phpmyadmin for visiting the MySQL GUI

After you reach this point all you need to download the SQL files and create a database in `phpmyadmin`. Call your new database `DM` so you don't get any errors during the import. 

Open your database from the left sidebar and click from the top tabs `Import`. Choose the file and click Go. You will notice that the tables have been created for you. Repeat this step for all the files in the `SQL-Files` directory.

##### Running SQL Queries
Once your database is set you can click from the top tabs `SQL` and start typing your query. 

###### Try these SQL queries.

This will give you the top 5 users based on the review count.
```SQL
SELECT 
    *
FROM
    `user`
ORDER BY `review_count` DESC
LIMIT 5
```

This will give you the top 10 Chinese restaurants based on the review count.
```SQL
SELECT 
    `name`, `full_address`, `review_count`
FROM
    business
WHERE
    `business_id` IN (SELECT 
            `business_id`
        FROM
            business_categories
        WHERE
            `category` = 'Chinese')
ORDER BY review_count DESC
LIMIT 10
```

This will calculate the average review count in the 15,585 businesses
```SQL
SELECT 
    AVG(`review_count`) as average_review_count
FROM
    `business`
```