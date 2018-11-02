/*
lmclient.go
*/

package main

import (
	"fmt"
	"io"
	"flag"
	"time"
	"os"
	"os/exec"
	"strings"
	"strconv"
	"encoding/json"
	"net/http"
	"bytes"
	"io/ioutil"
)

type JsonObject struct {
	Identifier string
	Objects interface{}
}

/*
Things that require updating
ATHLETES ->
RECORDS ->
SCHEDULE ->
MEET RESULTS ->
MEET ENTRIES <- (maybe ->)
*/

type Meet struct {
	Id int
	Opponent string
	DayOfMeet time.Time
	Location bool
	LocationAux string	
}

type Event struct {
	Id int
	Sex string
	AgeGroup string
	Distance int
	StrokeName string
	Order int
}

type Entry struct {
	ResMeet *Meet
	ResEvent *Event
	//ResTeam *Team
	ResAth []*Athlete
}

type MeetResult struct {
	ResEntry *Entry
	ResTime int	
}

type Record struct {
	RecEvent *Event
	RecDate time.Time
	RecTime int
	RecText string
}

type Athlete struct {
	Id string
	First string
	Last string
	Birth string
	IdNo string
	Sex string
}

type Parent struct {
	Email string
	FirstName string
	LastName string
	Password string
	Aux *Parent
}

var database string

func getQueryResult(query string) []string {
	cmd := exec.Command("mdb-sql", "-HFp", database)
	stdin, err := cmd.StdinPipe()
	if err != nil {
		panic(err)
	}

	io.WriteString(stdin, query)
	stdin.Close()

	result, err := cmd.CombinedOutput()
	if err != nil {
		panic(err)
	}

	return strings.Split(strings.Trim(string(result), "\n"), "\n")
}

func main() {
	endpoint := flag.String("domain", "http://lmswim.x10host.com/manager/api/athlete/update", "Domain of Lake Manassas website.")
	flag.StringVar(&database, "database", "", "MDB Database.")
	dbformat := flag.String("dbformat", "", "Format of database.")
	tasks := flag.String("tasks", "", "Tasks to complete.")
	method := flag.String("method", "REST", "Method of sending transmitting data")
	flag.Parse()

	if *dbformat == "" || *dbformat != "TM" && *dbformat != "MM" {
		os.Exit(1)
	}

	if *dbformat == "TM" {
		taskset := strings.Split(*tasks, ",")

		var jsonObjects []*JsonObject
		for _, task := range taskset {
			switch strings.ToUpper(strings.Trim(task, " ")) {
				case "ATHLETES":
					athletes := UpdateAthletes()
					jsonObjects = append(jsonObjects, &JsonObject{
						Identifier: "Athletes",
						Objects: athletes,
					})
					break
				case "RECORDS":
					records := UpdateRecords()
					jsonObjects = append(jsonObjects, &JsonObject{
						Identifier: "Records",
						Objects: records,
					})
					break
			}
		}

		jsonBytes, err := json.Marshal(jsonObjects)
		if err != nil {
			panic(err)
		}

		if *method == "PRINT" {
			data := string(jsonBytes)
			fmt.Println(data)
		} else if *method == "REST" {
			fmt.Println(*endpoint)
			resp, err := http.Post(*endpoint, "application/json", bytes.NewBuffer(jsonBytes))

			if err != nil {
				panic(err)
			}

			data, err := ioutil.ReadAll(resp.Body)
			if err != nil {
				panic(err)
			}
			fmt.Println(string(data))
		} else {
			panic("Invalid data transmission method!")
		}
	}
}

func UpdateAthletes() []*Athlete {
	rows := getQueryResult("Select ATHLETE,FIRST,LAST,BIRTH,ID_NO,SEX From ATHLETE Where INACTIVE = 0")
	athletes := make([]*Athlete, 0, len(rows))
	for i := 0; i < len(rows); i++ {
		row := strings.Split(rows[i], "\t")
		
		birth, _ := time.Parse("01/02/06 15:04:05", row[3])
		athlete := &Athlete{
			Id: row[0],
			First: row[1],
			Last: row[2],
			Birth: birth.Format("2006-01-02"),
			IdNo: row[4],
			Sex: row[5],
		}
		athletes = append(athletes, athlete)
	}

	return athletes
}

func GenerateEvent(loage int, hiage int, distance int, stroke int, sex string, i_r string) *Event {
	var eid int
	var agegroup string
	var strokename string
	var order int
	
	if loage == 0 {
		agegroup = fmt.Sprintf("%d & U", hiage)
	} else {
		agegroup = fmt.Sprintf("%d-%d", loage, hiage)
	}

	if loage == 0 && hiage == 8 && distance == 100 && stroke == 5 && sex == "X" && i_r == "R" { /* Medley Relay */
		eid = 1
		strokename = "Medley Relay"
	} else if loage == 9 && hiage == 10 && distance == 100 && stroke == 5 && sex == "M" && i_r == "R" {
		eid = 2
		strokename = "Medley Relay"
	} else if loage == 9 && hiage == 10 && distance == 100 && stroke == 5 && sex == "F" && i_r == "R" {
		eid = 3
		strokename = "Medley Relay"
	} else if loage == 11 && hiage == 12 && distance == 100 && stroke == 5 && sex == "M" && i_r == "R" {
		eid = 4
		strokename = "Medley Relay"
	} else if loage == 11 && hiage == 12 && distance == 100 && stroke == 5 && sex == "F" && i_r == "R" {
		eid = 5
		strokename = "Medley Relay"
	} else if loage == 13 && hiage == 14 && distance == 200 && stroke == 5 && sex == "M" && i_r == "R" {
		eid = 6
		strokename = "Medley Relay"
	} else if loage == 13 && hiage == 14 && distance == 200 && stroke == 5 && sex == "F" && i_r == "R" {
		eid = 7
		strokename = "Medley Relay"
	} else if loage == 15 && hiage == 18 && distance == 200 && stroke == 5 && sex == "M" && i_r == "R" {
		eid = 8
		strokename = "Medley Relay"
	} else if loage == 15 && hiage == 18 && distance == 200 && stroke == 5 && sex == "F" && i_r == "R" {
		eid = 9
		strokename = "Medley Relay"
	} else if loage == 0 && hiage == 8 && distance == 25 && stroke == 1 && sex == "M" && i_r == "I" { /* Free */
		eid = 10
		strokename = "Free"
	} else if loage == 0 && hiage == 8 && distance == 25 && stroke == 1 && sex == "F" && i_r == "I" {
		eid = 11
		strokename = "Free"		
	} else if loage == 9 && hiage == 10 && distance == 50 && stroke == 1 && sex == "M" && i_r == "I" {
		eid = 12
		strokename = "Free"
	} else if loage == 9 && hiage == 10 && distance == 50 && stroke == 1 && sex == "F" && i_r == "I" {
		eid = 13
		strokename = "Free"
	} else if loage == 11 && hiage == 12 && distance == 50 && stroke == 1 && sex == "M" && i_r == "I" {
		eid = 14
		strokename = "Free"
	} else if loage == 11 && hiage == 12 && distance == 50 && stroke == 1 && sex == "F" && i_r == "I" {
		eid = 15
		strokename = "Free"
	} else if loage == 13 && hiage == 14 && distance == 50 && stroke == 1 && sex == "M" && i_r == "I" {
		eid = 16
		strokename = "Free"
	} else if loage == 13 && hiage == 14 && distance == 50 && stroke == 1 && sex == "F" && i_r == "I" {
		eid = 17
		strokename = "Free"
	} else if loage == 15 && hiage == 18 && distance == 50 && stroke == 1 && sex == "M" && i_r == "I" {
		eid = 18
		strokename = "Free"
	} else if loage == 15 && hiage == 18 && distance == 50 && stroke == 1 && sex == "F" && i_r == "I" {
		eid = 19
		strokename = "Free"
	} else if loage == 0 && hiage == 10 && distance == 25 && stroke == 5 && sex == "M" && i_r == "I" { /* IM */
		eid = 20
		strokename = "IM"
	} else if loage == 0 && hiage == 10 && distance == 25 && stroke == 5 && sex == "F" && i_r == "I" {
		eid = 21
		strokename = "IM"
	}else if loage == 11 && hiage == 12 && distance == 50 && stroke == 5 && sex == "M" && i_r == "I" {
		eid = 22
		strokename = "IM"
	} else if loage == 11 && hiage == 12 && distance == 50 && stroke == 5 && sex == "F" && i_r == "I" {
		eid = 23
		strokename = "IM"
	} else if loage == 13 && hiage == 14 && distance == 50 && stroke == 5 && sex == "M" && i_r == "I" {
		eid = 24
		strokename = "IM"
	} else if loage == 13 && hiage == 14 && distance == 50 && stroke == 5 && sex == "F" && i_r == "I" {
		eid = 25
		strokename = "IM"
	} else if loage == 15 && hiage == 18 && distance == 50 && stroke == 5 && sex == "M" && i_r == "I" {
		eid = 26
		strokename = "IM"
	} else if loage == 15 && hiage == 18 && distance == 50 && stroke == 5 && sex == "F" && i_r == "I" {
		eid = 27
		strokename = "IM"
	} else if loage == 0 && hiage == 8 && distance == 25 && stroke == 2 && sex == "M" && i_r == "I" { /* Back */
		eid = 28
		strokename = "Back"
	} else if loage == 0 && hiage == 8 && distance == 25 && stroke == 2 && sex == "F" && i_r == "I" {
		eid = 29
		strokename = "Back"
	} else if loage == 9 && hiage == 10 && distance == 50 && stroke == 2 && sex == "M" && i_r == "I" {
		eid = 30
		strokename = "Back"
	} else if loage == 9 && hiage == 10 && distance == 50 && stroke == 2 && sex == "F" && i_r == "I" {
		eid = 31
		strokename = "Back"
	} else if loage == 11 && hiage == 12 && distance == 50 && stroke == 2 && sex == "M" && i_r == "I" {
		eid = 32
		strokename = "Back"
	} else if loage == 11 && hiage == 12 && distance == 50 && stroke == 2 && sex == "F" && i_r == "I" {
		eid = 33
		strokename = "Back"
	} else if loage == 13 && hiage == 14 && distance == 50 && stroke == 2 && sex == "M" && i_r == "I" {
		eid = 34
		strokename = "Back"
	} else if loage == 13 && hiage == 14 && distance == 50 && stroke == 2 && sex == "F" && i_r == "I" {
		eid = 35
		strokename = "Back"
	} else if loage == 15 && hiage == 18 && distance == 50 && stroke == 2 && sex == "M" && i_r == "I" {
		eid = 36
		strokename = "Back"
	} else if loage == 15 && hiage == 18 && distance == 50 && stroke == 2 && sex == "F" && i_r == "I" {
		eid = 37
		strokename = "Back"
	} else if loage == 0 && hiage == 8 && distance == 25 && stroke == 3 && sex == "M" && i_r == "I" { /* Breast */
		eid = 38
		strokename = "Breast"
	} else if loage == 0 && hiage == 8 && distance == 25 && stroke == 3 && sex == "F" && i_r == "I" {
		eid = 39
		strokename = "Breast"
	} else if loage == 9 && hiage == 10 && distance == 50 && stroke == 3 && sex == "M" && i_r == "I" {
		eid = 40
		strokename = "Breast"
	} else if loage == 9 && hiage == 10 && distance == 50 && stroke == 3 && sex == "F" && i_r == "I" {
		eid = 41
		strokename = "Breast"
	} else if loage == 11 && hiage == 12 && distance == 50 && stroke == 3 && sex == "M" && i_r == "I" {
		eid = 42
		strokename = "Breast"
	} else if loage == 11 && hiage == 12 && distance == 50 && stroke == 3 && sex == "F" && i_r == "I" {
		eid = 43
		strokename = "Breast"
	} else if loage == 13 && hiage == 14 && distance == 50 && stroke == 3 && sex == "M" && i_r == "I" {
		eid = 44
		strokename = "Breast"
	} else if loage == 13 && hiage == 14 && distance == 50 && stroke == 3 && sex == "F" && i_r == "I" {
		eid = 45
		strokename = "Breast"
	} else if loage == 15 && hiage == 18 && distance == 50 && stroke == 3 && sex == "M" && i_r == "I" {
		eid = 46
		strokename = "Breast"
	} else if loage == 15 && hiage == 18 && distance == 50 && stroke == 3 && sex == "F" && i_r == "I" {
		eid = 47
		strokename = "Breast"
	} else if loage == 0 && hiage == 8 && distance == 25 && stroke == 4 && sex == "M" && i_r == "I" { /* Fly */
		eid = 48
		strokename = "Fly"
	} else if loage == 0 && hiage == 8 && distance == 25 && stroke == 4 && sex == "F" && i_r == "I" {
		eid = 49
		strokename = "Fly"
	} else if loage == 9 && hiage == 10 && distance == 50 && stroke == 4 && sex == "M" && i_r == "I" {
		eid = 50
		strokename = "Fly"
	} else if loage == 9 && hiage == 10 && distance == 50 && stroke == 4 && sex == "F" && i_r == "I" {
		eid = 51
		strokename = "Fly"
	} else if loage == 11 && hiage == 12 && distance == 50 && stroke == 4 && sex == "M" && i_r == "I" {
		eid = 52
		strokename = "Fly"
	} else if loage == 11 && hiage == 12 && distance == 50 && stroke == 4 && sex == "F" && i_r == "I" {
		eid = 53
		strokename = "Fly"
	} else if loage == 13 && hiage == 14 && distance == 50 && stroke == 4 && sex == "M" && i_r == "I" {
		eid = 54
		strokename = "Fly"
	} else if loage == 13 && hiage == 14 && distance == 50 && stroke == 4 && sex == "F" && i_r == "I" {
		eid = 55
		strokename = "Fly"
	} else if loage == 15 && hiage == 18 && distance == 50 && stroke == 4 && sex == "M" && i_r == "I" {
		eid = 56
		strokename = "Fly"
	} else if loage == 15 && hiage == 18 && distance == 50 && stroke == 4 && sex == "F" && i_r == "I" {
		eid = 57
		strokename = "Fly"
	} else if loage == 0 && hiage == 8 && distance == 100 && stroke == 1 && sex == "M" && i_r == "R" { /* Free Medley */
		eid = 58
		 strokename = "Free Relay"
	} else if loage == 0 && hiage == 8 && distance == 100 && stroke == 1 && sex == "F" && i_r == "R" {
		eid = 59
		strokename = "Free Relay"
	} else if loage == 9 && hiage == 10 && distance == 100 && stroke == 1 && sex == "M" && i_r == "R" {
		eid = 60
		strokename = "Free Relay"
	} else if loage == 9 && hiage == 10 && distance == 100 && stroke == 1 && sex == "F" && i_r == "R" {
		eid = 61
		strokename = "Free Relay"
	} else if loage == 11 && hiage == 12 && distance == 100 && stroke == 1 && sex == "M" && i_r == "R" {
		eid = 62
		strokename = "Free Relay"
	} else if loage == 11 && hiage == 12 && distance == 100 && stroke == 1 && sex == "F" && i_r == "R" {
		eid = 63
		strokename = "Free Relay"
	} else if loage == 13 && hiage == 14 && distance == 100 && stroke == 1 && sex == "M" && i_r == "R" {
		eid = 64
		strokename = "Free Relay"
	} else if loage == 13 && hiage == 14 && distance == 100 && stroke == 1 && sex == "F" && i_r == "R" {
		eid = 65
		strokename = "Free Relay"
	} else if loage == 15 && hiage == 18 && distance == 100 && stroke == 1 && sex == "M" && i_r == "R" {
		eid = 66
		strokename = "Free Relay"
	} else if loage == 15 && hiage == 18 && distance == 100 && stroke == 1 && sex == "F" && i_r == "R" {
		eid = 67
		strokename = "Free Relay"
	}

	order = eid + 2
	if eid > 65 {
		order = eid % 65
	}

	return &Event{
		Id: eid,
		Sex: sex,
		AgeGroup: agegroup,
		Distance: distance,
		StrokeName: strokename,
		Order: order,
	}
		
}

func GenerateRecord(loage int, hiage int, distance int, stroke int, sex string, i_r string, recdate string, rectime int, rectext string) *Record {
	nrecdate, _ := time.Parse("01/02/06 15:04:05", recdate)

	return &Record{
		RecEvent: GenerateEvent(loage, hiage, distance, stroke, sex, i_r),
		RecDate: nrecdate,
		RecTime: rectime,
		RecText: rectext,
	}
}

func UpdateRecords() []*Record {
	rows := getQueryResult("Select RECORD From RECNAME Where RECFILE = 'LMST'")

	mostRecentRecFile := -1
	for _, value := range rows {		
		recFile, _ := strconv.Atoi(value)
		if recFile > mostRecentRecFile {
			mostRecentRecFile = recFile
		}
	}

	rows = getQueryResult(fmt.Sprintf("Select Lo_age,Hi_Age,Distance,Stroke,Sex,I_R,RecDate,RecTime,RecText From RECORDS Where Record=%d", mostRecentRecFile))

	records := make([]*Record, 0, 100)
	for i := 0; i < len(rows); i++ {
		row := strings.Split(rows[i], "\t")

		p0, _ := strconv.Atoi(row[0])
		p1, _ := strconv.Atoi(row[1])
		p2, _ := strconv.Atoi(row[2])
		p3, _ := strconv.Atoi(row[3])
		p7, _ := strconv.Atoi(row[7])
		
		records = append(records, GenerateRecord(p0, p1, p2, p3, row[4], row[5], row[6], p7, row[8]))		
	}

	return records
}