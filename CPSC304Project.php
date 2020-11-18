<html>
<head>
    <title>CPSC 304 Project</title>
</head>

<body>
<h2>Reset</h2>
<p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

<form method="POST" action="CPSC304Project.php">
    <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
    <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
    <p><input type="submit" value="Reset" name="reset"></p>
</form>

<hr />

<h2>Insert Values into Resident
</h2>
<form method="POST" action="CPSC304Project.php"> <!--refresh page when submitted-->
    <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
    Fname: <input type="text" name="insFname"> <br /><br />
    Lname: <input type="text" name="insLname"> <br /><br />
    Email : <input type="text" name="insEmail"> <br /><br />
    Phone : <input type="text" name="insPhone"> <br /><br />
    Dob (DD-MMM-YY) : <input type="text" name="insDob"> <br /><br />
    Resid (10-digit number): <input type="text" name="insResid"> <br /><br />
    <input type="submit" value="Insert" name="insertSubmit"></p>
</form>

<hr />


<h2>Display a Resident by ID (Select)</h2>
        <form method="GET" action="CPSC304Project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="DisplayResidentQueryRequest" name="DisplayResidentQueryRequest">
            Resident ID: <input type="text" name="insResidentID"> <br /><br />
            <input type="submit" name="selectResident"></p>
        </form>
        
<hr />


<h2>Project Building Attibutes for a given Building</h2>
        <form method="GET" action="CPSC304Project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="ProjectBuildingQueryRequest" name="ProjectBuildingQueryRequest">
            BuildingID: <input type="text" name="bid"> <br /><br />
            <input type="submit" name="projectBuilding"></p>
        </form>

<hr />


<h2>Update Phone in Resident</h2>
<p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

<form method="POST" action="CPSC304Project.php"> <!--refresh page when submitted-->
    <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
    Old Phone: <input type="text" name="oldPhone"> <br /><br />
    New Phone: <input type="text" name="newPhone"> <br /><br />

    <input type="submit" value="Update" name="updateSubmit"></p>
</form>

<hr />

<h2>Update Suite Rental Rate</h2>
<p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>
<form method="POST" action="CPSC304Project.php"> <!--refresh page when submitted-->
    <input type="hidden" id="updateRentalRateRequest" name="updateRentalRateRequest">
    Building: <input type="text" name="'buildingID'"> <br /><br />
    Suite: <input type="text" name="'unitNumber'"> <br /><br />
    New Rental Rate: <input type="text" name="updatedRate"> <br /><br /> 

    <input type="submit" value="Update" name="updateSubmit"></p>
</form>

<hr />

<h2>Count the Tuples in Resident
</h2>
<form method="GET" action="CPSC304Project.php"> <!--refresh page when submitted-->
    <input type="hidden" id="countTupleRequest" name="countTupleRequest">
    <input type="submit" name="countTuples"></p>
</form>

<hr />

<h2>Aggregation with GroupBy - get Number of Suites in each Building</h2>
        <form method="GET" action="CPSC304Project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="AggGroupByRequest" name="AggGroupByRequest">
            <input type="submit" name="AggGroupBy"></p>
        </form>

<hr />


<h2>Find Cheapest Building by Average Suite Cost</h2>
        <form method="GET" action="CPSC304Project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="cheapestAVGRequeset" name="cheapestAVGRequeset">
            <input type="submit" name="nestedAGG"></p>
        </form>

<hr />

<h2>Number of Stalls for Parkades above Ground</h2>
        <form method="GET" action="CPSC304Project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="parkadeStallsRequest" name="parkadeStallsRequest">
            <input type="submit" name="parkadeStalls"></p>
        </form>

<hr />

<h2>Display the Tables</h2>
<form method="GET" action="CPSC304Project.php"> <!--refresh page when submitted-->
    <input type="hidden" id="displayTupleRequest" name="displayTupleRequest">
    <input type="submit" name="displayTuples"></p>
</form>

<?php
//this tells the system that it's no longer just parsing html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = NULL; // edit the login credentials in connectToDB()
$show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

function debugAlertMessage($message) {
    global $show_debug_alert_messages;

    if ($show_debug_alert_messages) {
        echo "<script type='text/javascript'>alert('" . $message . "');</script>";
    }
}

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;

    $statement = OCIParse($db_conn, $cmdstr);
    //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
        echo htmlentities($e['message']);
        $success = False;
    }

    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
        echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
        echo htmlentities($e['message']);
        $success = False;
    }

    return $statement;
}

function executeBoundSQL($cmdstr, $list) {
    /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
In this case you don't need to create the statement several times. Bound variables cause a statement to only be
parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
See the sample code below for how this function is used */

    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = False;
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {
            //echo $val;
            //echo "<br>".$bind."<br>";
            OCIBindByName($statement, $bind, $val);
            unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
        }

        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
            echo htmlentities($e['message']);
            echo "<br>";
            $success = False;
        }
    }
}

function printResult($result) { //prints results from a select statement
    echo "<br>Retrieved data from table Resident:<br>";
    echo "<table>";
    echo "<tr><th>Fname</th><th>Lname</th><th>Email</th><th>Phone</th><th>Dob</th><th>Resid</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["FNAME"] . "</td><td>" . $row["LNAME"] . "</td><td>" . $row["EMAIL"] . "</td><td>" . $row["PHONE"] . "</td><td>" . $row["DOB"] . "</td><td>" . $row["RESID"] . "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

function printResult1($result) { //prints results from a select statement
    echo "<br>Retrieved data from table Owner:<br>";
    echo "<table>";
    echo "<tr><th>Purchasedate</th><th>Ownerid</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["PURCHASEDATE"] . "</td><td>" . $row["OWNERID"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

function printResult2($result) { //prints results from a select statement
    echo "<br>Retrieved data from table Renter:<br>";
    echo "<table>";
    echo "<tr><th>Startdate</th><th>Enddate</th><th>Renterid</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["STARTDATE"] . "</td><td>" . $row["ENDDATE"] .  "</td><td>" . $row["RENTERID"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

function printResult3($result) { //prints results from a select statement
    echo "<br>Retrieved data from table Parkade:<br>";
    echo "<table>";
    echo "<tr><th>Numstalls</th><th>Floor</th><th>Parkadeid</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["NUMSTALLS"] . "</td><td>" . $row["FLOOR"] .  "</td><td>" . $row["PARKADEID"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

function printResult4($result) { //prints results from a select statement
    echo "<br>Retrieved data from table VisitorStall:<br>";
    echo "<table>";
    echo "<tr><th>Hourlyrate</th><th>Stallnum</th><th>Stallid</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["HOURLYRATE"] . "</td><td>" . $row["STALLNUM"] .  "</td><td>" . $row["STALLID"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

function printResult5($result) { //prints results from a select statement
    echo "<br>Retrieved data from table ResidentStall:<br>";
    echo "<table>";
    echo "<tr><th>Residentid</th><th>Stallnum</th><th>Stallid</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["RESIDENTID"] . "</td><td>" . $row["STALLNUM"] .  "</td><td>" . $row["STALLID"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

function printResult6($result) { //prints results from a select statement
    echo "<br>Retrieved data from table Insurance:<br>";
    echo "<table>";
    echo "<tr><th>Companyname</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["COMPANYNAME"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}
function printResult7($result) { //prints results from a select statement
    echo "<br>Retrieved data from table Location1:<br>";
    echo "<table>";
    echo "<tr><th>Address</th><th>City</th><th>Postalcode</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["ADDRESS"] . "</td><td>" . $row["CITY"] .  "</td><td>" . $row["POSTALCODE"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

function printResult8($result) { //prints results from a select statement
    echo "<br>Retrieved data from table Location2:<br>";
    echo "<table>";
    echo "<tr><th>Postalcode</th><th>Province</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["POSTALCODE"] . "</td><td>" . $row["PROVINCE"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

function printResult9($result) { //prints results from a select statement
    echo "<br>Retrieved data from table Building:<br>";
    echo "<table>";
    echo "<tr><th>Buildingid</th><th>Buildingname</th><th>Yearbuilt</th><th>Numstories</th><th>Managerid</th><th>Insurancecompanyname</th><th>Address</th><th>City</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["BUILDINGID"] . "</td><td>" . $row["BUILDINGNAME"] .  "</td><td>" . $row["YEARBUILT"] .  "</td><td>" . $row["NUMSTORIES"] .  "</td><td>" . $row["MANAGERID"] .  "</td><td>" . $row["INSURANCECOMPANYNAME"] .  "</td><td>" . $row["ADDRESS"] .  "</td><td>" . $row["CITY"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

function printResult10($result) { //prints results from a select statement
    echo "<br>Retrieved data from table Suites:<br>";
    echo "<table>";
    echo "<tr><th>Floor</th><th>Squarefootage</th><th>Numbedrooms</th><th>Numwashrooms</th><th>Rentalrate</th><th>Cost</th><th>Unitnum</th><th>Contractsto</th><th>SuiteBuildingid</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["FLOOR"] . "</td><td>" . $row["SQUAREFOOTAGE"] .  "</td><td>" . $row["NUMBEDROOMS"] .  "</td><td>" . $row["NUMWASHROOMS"] .  "</td><td>" . $row["RENTALRATE"] .  "</td><td>" . $row["COST"] .  "</td><td>" . $row["UNITNUM"] .  "</td><td>" . $row["CONTRACTSTO"] .  "</td><td>" . $row["SUITEBUILDINGID"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

function printResult11($result) { //prints results from a select statement
    echo "<br>Retrieved data from table Manager1:<br>";
    echo "<table>";
    echo "<tr><th>ManagerPhone</th><th>managerEmail</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["MANAGERPHONE"] . "</td><td>" . $row["MANAGEREMAIL"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

function printResult12($result) { //prints results from a select statement
    echo "<br>Retrieved data from table Manager2:<br>";
    echo "<table>";
    echo "<tr><th>ManagerID</th><th>ManagerName</th><th>ManagerPhone</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["MANAGERID"] . "</td><td>" . $row["MANAGERNAME"] .  "</td><td>" . $row["MANAGERPHONE"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

function printResult13($result) { //prints results from a select statement
    echo "<br>Retrieved data from table Elevator:<br>";
    echo "<table>";
    echo "<tr><th>UnderMaintenance</th><th>StartFloor</th><th>EndFloor</th><th>ElevatorId</th><th>InBuilding</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["UNDERMAINTENANCE"] . "</td><td>" . $row["STARTFLOOR"] .  "</td><td>" . $row["ENDFLOOR"] .  "</td><td>" . $row["ELEVATORID"] .  "</td><td>" . $row["INBUILDING"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}
function printResult14($result) { //prints results from a select statement
    echo "<br>Retrieved data from table Stairs:<br>";
    echo "<table>";
    echo "<tr><th>StartFloor</th><th>EndFloor</th><th>StairsID</th><th>InBuilding</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["STARTFLOOR"] . "</td><td>" . $row["ENDFLOOR"] .  "</td><td>" . $row["STAIRSID"] .  "</td><td>" . $row["INBUILDING"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}





function connectToDB() {
    global $db_conn;

    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.
    $db_conn = OCILogon("ora_chalis01", "a69826675", "dbhost.students.cs.ubc.ca:1522/stu");

    if ($db_conn) {
        debugAlertMessage("Database is Connected");
        return true;
    } else {
        debugAlertMessage("Cannot connect to Database");
        $e = OCI_Error(); // For OCILogon errors pass no handle
        echo htmlentities($e['message']);
        return false;
    }
}

function disconnectFromDB() {
    global $db_conn;

    debugAlertMessage("Disconnect from Database");
    OCILogoff($db_conn);
}

function handleUpdateRequest() {
    global $db_conn;

    $old_phone = $_POST['oldPhone'];
    $new_phone = $_POST['newPhone'];

    // you need the wrap the old name and new name values with single quotations
    executePlainSQL("UPDATE Resident SET Phone='" . $new_phone . "' WHERE Phone='" . $old_phone . "'");
    OCICommit($db_conn);
}

function handleUpdateSuiteRentalRate() {
    global $db_comm;

    $unit_number = $_POST['unitNumber'];
    $buildingID = $_POST['buildingID'];

    $new_rate = $_POST['updatedRate'];

    executePlainSQL("UPDATE Suites SET Rentalrate='" . $new_rate . "' WHERE Unitnum='" . $unit_number . "'");
}

function handleResetRequest() {
    global $db_conn;
    // Drop old table
    executePlainSQL("DROP TABLE Resident CASCADE CONSTRAINTS");
    executePlainSQL("DROP TABLE Owner");
    executePlainSQL("DROP TABLE Renter");
    executePlainSQL("DROP TABLE Parkade CASCADE CONSTRAINTS");
    executePlainSQL("DROP TABLE VisitorStall");
    executePlainSQL("DROP TABLE ResidentStall");
    executePlainSQL("DROP TABLE Insurance CASCADE CONSTRAINTS");
    executePlainSQL("DROP TABLE Location1 CASCADE CONSTRAINTS");
    executePlainSQL("DROP TABLE Location2 CASCADE CONSTRAINTS");
    executePlainSQL("DROP TABLE Manager1 CASCADE CONSTRAINTS");
    executePlainSQL("DROP TABLE Manager2 CASCADE CONSTRAINTS");
    executePlainSQL("DROP TABLE Building CASCADE CONSTRAINTS");
    executePlainSQL("DROP TABLE Suites CASCADE CONSTRAINTS");
    executePlainSQL("DROP TABLE Elevator CASCADE CONSTRAINTS");
    executePlainSQL("DROP TABLE Stairs CASCADE CONSTRAINTS");


    // Create new table
    echo "<br> creating new tables <br>";
    executePlainSQL("CREATE TABLE Resident (Fname char(30) , Lname char(30), Email char(30), Phone char(20), Dob date, Resid char(20), PRIMARY KEY (Resid) )");
    executePlainSQL("INSERT INTO Resident (Fname, Lname, Email, Phone, Dob, Resid) VALUES ('Eugene', 'Kwon', 'cofee.gratia@gmail.com','232-144-5599', '30-AUG-1945','7777777776') ");
    executePlainSQL("INSERT INTO Resident (Fname, Lname, Email, Phone, Dob, Resid) VALUES ('Jay', 'Reyes', 'Reyes_998@hotmail.com','778-778-7778', '20-FEB-2000','4584466779') ");
    executePlainSQL("INSERT INTO Resident (Fname, Lname, Email, Phone, Dob, Resid) VALUES ('Eric', 'Novelas', 'jjj.lanejay@gmail.com','604-432-77789', '30-APR-1998','6677889910') ");
    executePlainSQL("INSERT INTO Resident (Fname, Lname, Email, Phone, Dob, Resid) VALUES ('Melanie', 'Ng', 'brows_bymel@outlook.ca','226-145-8897', '31-MAY-1990','0023456789') ");
    executePlainSQL("INSERT INTO Resident (Fname, Lname, Email, Phone, Dob, Resid) VALUES ('Aoi', 'Kumabe', 'chacha_mamee@gmail.com','664-661-3320', '02-MAY-1994','9876543210') ");
    executePlainSQL("INSERT INTO Resident (Fname, Lname, Email, Phone, Dob, Resid) VALUES ('Sakina', 'Kaur', 'skaur@gmail.com','506-822-7654', '22-AUG-2008','31316439577') ");
    executePlainSQL("INSERT INTO Resident (Fname, Lname, Email, Phone, Dob, Resid) VALUES ('Angel', 'Gibbs', 'agibbs@gmail.com','456-456-3847', '12-SEP-2010','24283453736') ");
    executePlainSQL("INSERT INTO Resident (Fname, Lname, Email, Phone, Dob, Resid) VALUES ('Akina', 'Ng', 'akinang@gmail.com','273-287-2836', '10-FEB-2000','41313126753') ");


    executePlainSQL("CREATE TABLE Owner(Purchasedate date , Ownerid char(20), PRIMARY KEY (Ownerid), CONSTRAINT fk_owner FOREIGN KEY(Ownerid) REFERENCES Resident(Resid) ON DELETE CASCADE )");
    executePlainSQL("INSERT INTO Owner (Purchasedate, Ownerid) VALUES ('25-JUN-2010', '7777777776')");
    executePlainSQL("INSERT INTO Owner (Purchasedate, Ownerid) VALUES ('19-SEP-2012', '9876543210')");
    executePlainSQL("INSERT INTO Owner (Purchasedate, Ownerid) VALUES ('03-JUL-2009', '31316439577')");
    executePlainSQL("INSERT INTO Owner (Purchasedate, Ownerid) VALUES ('11-SEP-2019', '24283453736')");
    executePlainSQL("INSERT INTO Owner (Purchasedate, Ownerid) VALUES ('21-OCT-2006', '41313126753')");

    executePlainSQL("CREATE TABLE Renter(Startdate date, Enddate date, Renterid char(20), PRIMARY KEY (Renterid), CONSTRAINT fk_renter FOREIGN KEY(Renterid) REFERENCES Resident(Resid) ON DELETE CASCADE )");
    executePlainSQL("INSERT INTO Renter (Startdate, Enddate, Renterid) VALUES ('19-AUG-2010',NULL,'4584466779')");
    executePlainSQL("INSERT INTO Renter (Startdate, Enddate, Renterid) VALUES ('26-SEP-2019','26-SEP-2020','6677889910')");
    executePlainSQL("INSERT INTO Renter (Startdate, Enddate, Renterid) VALUES ('06-OCT-2015','06-APR-2020','0023456789')");
    executePlainSQL("INSERT INTO Renter (Startdate, Enddate, Renterid) VALUES ('21-MAR-2020',NULL,'24283453736')");
    executePlainSQL("INSERT INTO Renter (Startdate, Enddate, Renterid) VALUES ('22-SEP-2020','22-SEP-2021','41313126753')");

    executePlainSQL("CREATE TABLE Parkade(Numstalls INTEGER, Floor INTEGER, Parkadeid char(20), PRIMARY KEY(Parkadeid) )");
    executePlainSQL("INSERT INTO Parkade(Numstalls,Floor,Parkadeid) VALUES (100,-1,'7787787787') ");
    executePlainSQL("INSERT INTO Parkade(Numstalls,Floor,Parkadeid) VALUES (52,3,'3216549870') ");
    executePlainSQL("INSERT INTO Parkade(Numstalls,Floor,Parkadeid) VALUES (181,4,'9638527410') ");
    executePlainSQL("INSERT INTO Parkade(Numstalls,Floor,Parkadeid) VALUES (22,0,'7894561230') ");
    executePlainSQL("INSERT INTO Parkade(Numstalls,Floor,Parkadeid) VALUES (201,-3,'7539518246') ");

    executePlainSQL("CREATE TABLE VisitorStall(Hourlyrate NUMBER(5,2), StallNum INTEGER, Stallid char(20), PRIMARY KEY(StallNum,Stallid), CONSTRAINT fk_visstall FOREIGN KEY(Stallid) REFERENCES Parkade(Parkadeid) ON DELETE CASCADE)");
    executePlainSQL("INSERT INTO VisitorStall(Hourlyrate,StallNum,Stallid) VALUES (3.50,99,'7787787787') ");
    executePlainSQL("INSERT INTO VisitorStall(Hourlyrate,StallNum,Stallid) VALUES (3.50,46,'3216549870') ");
    executePlainSQL("INSERT INTO VisitorStall(Hourlyrate,StallNum,Stallid) VALUES (4.00,1,'9638527410') ");
    executePlainSQL("INSERT INTO VisitorStall(Hourlyrate,StallNum,Stallid) VALUES (1.50, 2,'7894561230') ");
    executePlainSQL("INSERT INTO VisitorStall(Hourlyrate,StallNum,Stallid) VALUES (0.5,103,'7539518246') ");

    executePlainSQL("CREATE TABLE ResidentStall(Residentid char(20), StallNum INTEGER, Stallid char(20), PRIMARY KEY(StallNum,Stallid), CONSTRAINT fk1_resstall FOREIGN KEY(Residentid) REFERENCES Resident(Resid) ON DELETE SET NULL, CONSTRAINT fk2_resstall FOREIGN KEY(Stallid) REFERENCES Parkade(Parkadeid) ON DELETE CASCADE )");
    executePlainSQL("INSERT INTO ResidentStall(Residentid, StallNum, Stallid) VALUES ('7777777776',99,'7787787787') ");
    executePlainSQL("INSERT INTO ResidentStall(Residentid, StallNum, Stallid) VALUES ('4584466779',100,'7787787787') ");
    executePlainSQL("INSERT INTO ResidentStall(Residentid, StallNum, Stallid) VALUES ('6677889910',5,'7894561230') ");
    executePlainSQL("INSERT INTO ResidentStall(Residentid, StallNum, Stallid) VALUES ('0023456789',179,'9638527410') ");
    executePlainSQL("INSERT INTO ResidentStall(Residentid, StallNum, Stallid) VALUES ('9876543210',14,'3216549870') ");

    executePlainSQL("CREATE TABLE Insurance(Companyname char(30), PRIMARY KEY(Companyname))");
    executePlainSQL("INSERT INTO Insurance(Companyname) VALUES ('RBC Insurance')");
    executePlainSQL("INSERT INTO Insurance(Companyname) VALUES ('Empire Life')");
    executePlainSQL("INSERT INTO Insurance(Companyname) VALUES ('Manulife Financial')");
    executePlainSQL("INSERT INTO Insurance(Companyname) VALUES ('Great-West Lifeco')");
    executePlainSQL("INSERT INTO Insurance(Companyname) VALUES ('IA Financial')");

    executePlainSQL("CREATE TABLE Location1(Address char(30), City char(30), Postalcode char(30), PRIMARY KEY(Address, City))");
    executePlainSQL("INSERT INTO Location1(Address,City,Postalcode) VALUES ('18 Marina Blvd','Toronto','M5H')");
    executePlainSQL("INSERT INTO Location1(Address,City,Postalcode) VALUES ('46 Mount Vernon Rd','Montreal','H3A')");
    executePlainSQL("INSERT INTO Location1(Address,City,Postalcode) VALUES ('804 Robson Street','Vancouver','V5K 1B2')");
    executePlainSQL("INSERT INTO Location1(Address,City,Postalcode) VALUES ('34 Bayshore Rd','Ottawa','K1A 1L8')");
    executePlainSQL("INSERT INTO Location1(Address,City,Postalcode) VALUES ('100 Copperfield Grove','Calgary','T2C')");

    executePlainSQL("CREATE TABLE Location2(Postalcode char(30), Province char(30), PRIMARY KEY(Postalcode))");
    executePlainSQL("INSERT INTO Location2(Postalcode, Province) VALUES ('M5H', 'Ontario')");
    executePlainSQL("INSERT INTO Location2(Postalcode, Province) VALUES ('H3A', 'Quebec')");
    executePlainSQL("INSERT INTO Location2(Postalcode, Province) VALUES ('V5K 1B2', 'British Columbia')");
    executePlainSQL("INSERT INTO Location2(Postalcode, Province) VALUES ('K1A 1L8', 'Ontario')");
    executePlainSQL("INSERT INTO Location2(Postalcode, Province) VALUES ('T2C', 'Alberta')");


    executePlainSQL("CREATE TABLE Manager1(ManagerPhone char(20), ManagerEmail char(30), PRIMARY KEY (ManagerPhone) )");
    executePlainSQL("INSERT INTO Manager1(ManagerPhone,ManagerEmail) VALUES ('604-939-2010','teslastocktoohigh@elon.ca')");
    executePlainSQL("INSERT INTO Manager1(ManagerPhone,ManagerEmail) VALUES ('403-778-7788','notthewhitehouse@obama.ca')");
    executePlainSQL("INSERT INTO Manager1(ManagerPhone,ManagerEmail) VALUES ('221-221-2212','mongolia@mongolia.ca')");
    executePlainSQL("INSERT INTO Manager1(ManagerPhone,ManagerEmail) VALUES ('999-211-7382','ramen@ramen.ca')");
    executePlainSQL("INSERT INTO Manager1(ManagerPhone,ManagerEmail) VALUES ('888-911-7777','smith@john.ca')");

    executePlainSQL("CREATE TABLE Manager2(ManagerID char(20), ManagerName char(30), ManagerPhone char(20), PRIMARY KEY (ManagerID), CONSTRAINT fk_manager2 FOREIGN KEY(ManagerPhone) REFERENCES Manager1(ManagerPhone) ON DELETE CASCADE)");
    executePlainSQL("INSERT INTO Manager2(ManagerID,ManagerName,ManagerPhone) VALUES ('1000000000','Elon Musk','604-939-2010')");
    executePlainSQL("INSERT INTO Manager2(ManagerID,ManagerName,ManagerPhone) VALUES ('1000000001','Barack Obama','403-778-7788')");
    executePlainSQL("INSERT INTO Manager2(ManagerID,ManagerName,ManagerPhone) VALUES ('1000000002','Khaltmaagiin Battulga','221-221-2212')");
    executePlainSQL("INSERT INTO Manager2(ManagerID,ManagerName,ManagerPhone) VALUES ('1000000003','Momofuku Ando','999-211-7382')");
    executePlainSQL("INSERT INTO Manager2(ManagerID,ManagerName,ManagerPhone) VALUES ('1000000004','John Smith','888-911-7777')");


    executePlainSQL("CREATE TABLE Building(Buildingid char(20), Buildingname char(20), Yearbuilt INTEGER, Numstories INTEGER, Managerid char(20), Insurancecompanyname char(30), Address char(30), City char(30),PRIMARY KEY(Buildingid), CONSTRAINT fk_building FOREIGN KEY(Insurancecompanyname) REFERENCES Insurance(Companyname) ON DELETE CASCADE,CONSTRAINT fk2_building FOREIGN KEY(Address,City) REFERENCES Location1(Address,City) ON DELETE CASCADE, CONSTRAINT fk3_building FOREIGN KEY(Managerid) REFERENCES Manager2(ManagerID) ON DELETE SET NULL)");
    executePlainSQL("INSERT INTO Building(Buildingid,Buildingname, Yearbuilt, Numstories, Managerid, Insurancecompanyname, Address,City) VALUES ('1112223344','Bayview Tower',2000, 20,'1000000000', 'RBC Insurance', '18 Marina Blvd','Toronto')");
    executePlainSQL("INSERT INTO Building(Buildingid,Buildingname, Yearbuilt, Numstories, Managerid, Insurancecompanyname, Address,City) VALUES ('3355667788','Pacific Sands',2005, 30,'1000000004', 'Empire Life', '46 Mount Vernon Rd','Montreal')");
    executePlainSQL("INSERT INTO Building(Buildingid,Buildingname, Yearbuilt, Numstories, Managerid, Insurancecompanyname, Address,City) VALUES ('1133445566','Kerrisdale Towers',2010, 25,'1000000003','Great-West Lifeco','804 Robson Street','Vancouver')");
    executePlainSQL("INSERT INTO Building(Buildingid,Buildingname, Yearbuilt, Numstories, Managerid, Insurancecompanyname, Address,City) VALUES ('2288990077','Bellevue Towers',2019, 50,'1000000004', 'IA Financial', '34 Bayshore Rd','Ottawa')");
    executePlainSQL("INSERT INTO Building(Buildingid,Buildingname, Yearbuilt, Numstories, Managerid, Insurancecompanyname, Address,City) VALUES ('2266779988','Willow Gardens',2015, 32,'1000000001','Manulife Financial', '100 Copperfield Grove','Calgary')");

    executePlainSQL("CREATE TABLE Suites(Floor INTEGER, Squarefootage NUMBER, Numbedrooms INTEGER, Numwashrooms INTEGER, Rentalrate NUMBER, Cost NUMBER, Unitnum INTEGER, Contractsto char(20), SuiteBuildingid char(20), PRIMARY KEY (Unitnum,SuiteBuildingid),CONSTRAINT fk_suite FOREIGN KEY(Contractsto) REFERENCES Resident(Resid) ON DELETE SET NULL, CONSTRAINT fk2_suite FOREIGN KEY(SuiteBuildingid) REFERENCES Building(Buildingid) ON DELETE CASCADE )");
    executePlainSQL("INSERT INTO Suites(Floor,Squarefootage,Numbedrooms,Numwashrooms,Rentalrate,Cost,Unitnum,Contractsto,SuiteBuildingid) VALUES (1, 2200, 3,2,2100,NULL,102,'7777777776', '1112223344')");
    executePlainSQL("INSERT INTO Suites(Floor,Squarefootage,Numbedrooms,Numwashrooms,Rentalrate,Cost,Unitnum,Contractsto,SuiteBuildingid) VALUES (1, 3122, 4,2,3200,650000,103,'4584466779', '1112223344')");
    executePlainSQL("INSERT INTO Suites(Floor,Squarefootage,Numbedrooms,Numwashrooms,Rentalrate,Cost,Unitnum,Contractsto,SuiteBuildingid) VALUES (2, 772, 1,1,900,200000,207,'6677889910', '3355667788')");
    executePlainSQL("INSERT INTO Suites(Floor,Squarefootage,Numbedrooms,Numwashrooms,Rentalrate,Cost,Unitnum,Contractsto,SuiteBuildingid) VALUES (4, 420, 1,1,600,130000,403,'0023456789', '1133445566')");
    executePlainSQL("INSERT INTO Suites(Floor,Squarefootage,Numbedrooms,Numwashrooms,Rentalrate,Cost,Unitnum,Contractsto,SuiteBuildingid) VALUES (5, 988, 2,1,1200,300000,511,'9876543210', '2266779988')");

    executePlainSQL("CREATE TABLE Elevator (UnderMaintenance char(1),StartFloor INTEGER,EndFloor INTEGER, ElevatorId INTEGER, InBuilding char(20), PRIMARY KEY(ElevatorId), CONSTRAINT fk_elevator FOREIGN KEY(InBuilding) REFERENCES Building(Buildingid) ON DELETE CASCADE )");
    executePlainSQL("INSERT INTO Elevator(UnderMaintenance,StartFloor,EndFloor, ElevatorId, InBuilding) VALUES ('N', 1, 5,0007, '1112223344')");
    executePlainSQL("INSERT INTO Elevator(UnderMaintenance,StartFloor,EndFloor, ElevatorId, InBuilding) VALUES ('Y', 6, 10,0008, '1112223344')");
    executePlainSQL("INSERT INTO Elevator(UnderMaintenance,StartFloor,EndFloor, ElevatorId, InBuilding) VALUES ('N', 4, 10,0009, '3355667788')");
    executePlainSQL("INSERT INTO Elevator(UnderMaintenance,StartFloor,EndFloor, ElevatorId, InBuilding) VALUES ('N', 1, 20,1001, '2288990077')");
    executePlainSQL("INSERT INTO Elevator(UnderMaintenance,StartFloor,EndFloor, ElevatorId, InBuilding) VALUES ('Y', 2, 15,1002, '2266779988')");

    executePlainSQL("CREATE TABLE Stairs(StartFloor INTEGER,EndFloor INTEGER, StairsId INTEGER, InBuilding char(20), PRIMARY KEY(StairsId), CONSTRAINT fk_stairs FOREIGN KEY(InBuilding) REFERENCES Building(Buildingid) ON DELETE CASCADE )");
    executePlainSQL("INSERT INTO Stairs(StartFloor,EndFloor,StairsId,InBuilding) VALUES(2,3,0001,'2266779988')");
    executePlainSQL("INSERT INTO Stairs(StartFloor,EndFloor,StairsId,InBuilding) VALUES(1,10,0002,'1112223344')");
    executePlainSQL("INSERT INTO Stairs(StartFloor,EndFloor,StairsId,InBuilding) VALUES(4,6,0003,'2266779988')");
    executePlainSQL("INSERT INTO Stairs(StartFloor,EndFloor,StairsId,InBuilding) VALUES(1,8,0004,'3355667788')");
    executePlainSQL("INSERT INTO Stairs(StartFloor,EndFloor,StairsId,InBuilding) VALUES(3,10,0005,'1133445566')");


    OCICommit($db_conn);
}

function handleInsertRequest() {
    global $db_conn;

    //Getting the values from user and insert data into the table
    $tuple = array (
        ":bind1" => $_POST['insFname'],
        ":bind2" => $_POST['insLname'],
        ":bind3" => $_POST['insEmail'],
        ":bind4" => $_POST['insPhone'],
        ":bind5" => $_POST['insDob'],
        ":bind6" => $_POST['insResid'],
    );

    $alltuples = array (
        $tuple
    );

    executeBoundSQL("insert into Resident values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6)", $alltuples);
    OCICommit($db_conn);
}

function handleResidentSelectRequest() {
    global $db_conn;

    $resID = $_POST['insResidentID'];
    $result = executePlainSQL("SELECT * FROM Resident WHERE Resid = " . $_GET['insResidentID']);
    
    echo printResult($result);
}

function handleBuildingAttributeProjectRequest() {
    global $db_conn;

    $city = $_POST['bid'];
    $result = executePlainSQL("SELECT  Buildingname, Yearbuilt, Numstories FROM Building WHERE Buildingid  =  " . $_GET['bid']);
    
    echo printResult9($result);
}

// Aggregation with Having
function handleParkadeAboveGround() {
    global $db_comm;

    $result = executePlainSQL("SELECT SUM(Numstalls), Parkadeid FROM Parkade GROUP BY Parkadeid HAVING SUM(Floor) >= 0");

    echo "<br>Retrieved data from table Parkade:<br>";
    echo "<table>";
    echo "<tr><th>Number of Stalls</th><th>Parkade ID</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

function handleAggGroupByRequest() {
    global $db_conn;

    $result = executePlainSQL("SELECT COUNT(*), SuiteBuildingid FROM Suites GROUP BY SuiteBuildingid");
    
    echo "<br>Retrieved data from table Suites:<br>";
    echo "<table>";
    echo "<tr><th>Count</th><th>SuiteBuildingid</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row["SUITEBUILDINGID"] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

// Nested Aggegation with Grouping
function handleFindCheapestAverageRequest() {
    global $db_conn;

    $result = executePlainSQL("SELECT Temp.SuiteBuildingid, Temp.avgcost 
    FROM (SELECT Suites.SuiteBuildingid , AVG(Suites.Cost) as avgcost 
          FROM Suites  
          GROUP BY Suites.SuiteBuildingid) AS Temp
    WHERE Temp.avgcost = (SELECT MIN(Temp.avgcost) FROM Temp)");

   

    echo "<br>Retrieved data from table Suites:<br>";
    echo "<table>";
    echo "<tr><th>BuildingID</th><th>Average Cost</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] .  "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
}

function handleCountRequest() {
    global $db_conn;

    $result = executePlainSQL("SELECT Count(*) FROM Resident");

    if (($row = oci_fetch_row($result)) != false) {
        echo "<br> The number of tuples in Resident: " . $row[0] . "<br>";
    }
}

function handleDisplayRequest() {
    global $db_conn;
    printResult(executePlainSQL("SELECT * FROM Resident"));
    printResult1(executePlainSQL("SELECT * FROM Owner"));
    printResult2(executePlainSQL("SELECT * FROM Renter"));
    printResult3(executePlainSQL("SELECT * FROM Parkade"));
    printResult4(executePlainSQL("SELECT * FROM VisitorStall"));
    printResult5(executePlainSQL("SELECT * FROM ResidentStall"));
    printResult6(executePlainSQL("SELECT * FROM Insurance"));
    printResult7(executePlainSQL("SELECT * FROM Location1"));
    printResult8(executePlainSQL("SELECT * FROM Location2"));
    printResult9(executePlainSQL("SELECT * FROM Building"));
    printResult10(executePlainSQL("SELECT * FROM Suites"));
    printResult11(executePlainSQL("SELECT * FROM Manager1"));
    printResult12(executePlainSQL("SELECT * FROM Manager2"));
    printResult13(executePlainSQL("SELECT * FROM Elevator"));
    printResult14(executePlainSQL("SELECT * FROM Stairs"));

}





// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('resetTablesRequest', $_POST)) {
            handleResetRequest();
        } else if (array_key_exists('updateQueryRequest', $_POST)) {
            handleUpdateRequest();
        } else if (array_key_exists('insertQueryRequest', $_POST)) {
            handleInsertRequest();
        } else if (array_key_exists('updateRentalRateRequest', $_POST)) {
            handleUpdateSuiteRentalRate();
        }

        disconnectFromDB();
    }
}

// HANDLE ALL GET ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handleGETRequest() {
    if (connectToDB()) {
        if (array_key_exists('countTuples', $_GET)) {
            handleCountRequest();
        } else if (array_key_exists('displayTuples', $_GET)) {
            handleDisplayRequest();
        } else if (array_key_exists('selectResident', $_GET)) {
            handleResidentSelectRequest();
        } else if (array_key_exists('AggGroupBy', $_GET)) {
            handleAggGroupByRequest();
        }  else if (array_key_exists('projectBuilding', $_GET)) {
            handleBuildingAttributeProjectRequest();
        } else if (array_key_exists('nestedAGG', $_GET)) {
            handleFindCheapestAverageRequest();
        } else if (array_key_exists('parkadeStalls', $_GET)) {
            handleParkadeAboveGround();
        }
        disconnectFromDB();
    }
}

if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
    handlePOSTRequest();
} else if (isset($_GET['countTupleRequest'])|| 
isset($_GET['displayTupleRequest']) || 
isset($_GET['DisplayResidentQueryRequest']) || 
isset($_GET['AggGroupByRequest']) || 
isset($_GET['ProjectBuildingQueryRequest']) ||
isset($_GET['cheapestAVGRequeset']) ||
isset($_GET['parkadeStallsRequest'])) {
    handleGETRequest();
}
?>
</body>
</html>