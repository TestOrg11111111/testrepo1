import json
import os

from datetime import *

import numpy


# loads the json file data
def loadJSON(file):
    with open(file, 'r') as myfile:
        data = myfile.read()

    data = json.loads(data)


    return data

# fetches the total number of PRs (open and closed) given the json file
def getTotalPRs(file):
    data = loadJSON(file)

    #per day
    PRs_opened = []
    PRs_closed = []


    for pr in data:
        if pr['state'] == "open":
            PRs_opened.append(pr)
        else:
            PRs_closed.append(pr)

    print("Total open PRs: ", len(PRs_opened))
    print("Total closed PRs: ", len(PRs_closed))

#fetches the PRs from github (state: open, closed or all of them)
def getPRs(page, state):
    os.system("curl -o pullRequests_"+state+str(page)+".json https://api.github.com/repos/joomla/joomla-cms/pulls\?state\="+state+"\&page\="+str(page)+"\&per_page\=100")

# downloads several pages of PRs
def downloadPRs(pages, state):
    for i in range(1, pages+1):
        getPRs(i, state)

# returns a date range object given start and end date
def daterange(start_date, end_date):
    for n in range(int ((end_date - start_date).days)+1):
        yield start_date + timedelta(n)

# Average for how long a PR is live given latest closed PRs
def historyPRsAlive(pages):
    time = 0
    pr_number = 1
    PRs = []

    for i in range(1, pages+1):
        data = loadJSON("pullRequests_closed"+str(i)+".json")

        for pr in data:
            days = (datetime.strptime(pr['closed_at'].split("T")[0], "%Y-%m-%d") \
            - datetime.strptime(pr['created_at'].split("T")[0], "%Y-%m-%d")).days

            if days > 0:
                PRs.append(days)
                time += days
                pr_number+=1

    avg_time = time/pr_number

    print("Latest closed PRs were alive on average ", avg_time, " days. (Latest ", pages*100, " closed PRs)")
    print("\nStandard Deviation: ", numpy.std(PRs))
    #print("\n",sorted(PRs))

# Gets the PRs per day given a date range
def PRs_per_day(pages, dates_range):
    date_range = daterange(datetime.strptime(dates_range[0], "%Y-%m-%d"),\
                           datetime.strptime(dates_range[1], "%Y-%m-%d"))

    #per day
    PRs_opened = {}
    PRs_closed = {}

    prs_o = 0
    prs_c = 0

    for date in date_range:
        day = date.strftime("%Y-%m-%d")
        for i in range(1, pages+1):
            data = loadJSON("pullRequests_all"+str(i)+".json")

            for pr in data:
                if day in pr['created_at']:
                    prs_o+=1
                elif pr['closed_at'] != None and day in pr['closed_at']:
                    prs_c+=1

        PRs_opened[date] = prs_o
        PRs_closed[date] = prs_c

        prs_o = 0
        prs_c = 0

    PRs_opened_vals = sorted(PRs_opened.values())
    PRs_closed_vals = sorted(PRs_closed.values())

    PRs_opened = sorted(PRs_opened.items(), key=lambda t: t[0])
    PRs_closed = sorted(PRs_closed.items(), key=lambda t: t[0])

    print("Opened PRs from "+dates_range[0] +" to "+dates_range[1]+": \n")
    for pr in PRs_opened:
        print(pr[0], ":", pr[1])

    print("\n")

    print("Closed PRs from "+dates_range[0] +" to "+dates_range[1]+": \n")
    for pr in PRs_closed:
        print(pr[0], ":", pr[1])

    print("Average PRs opened per day: ", numpy.average(PRs_opened_vals))
    print("Average PRs closed per day: ", numpy.average(PRs_closed_vals))

"""
Before checking PRs per day and the average life of a PR one
must first download the Jsons
"""

#downloadPRs(4, "open")
#historyPRsAlive(20)
PRs_per_day(20, ('2017-03-01', '2017-03-28'))
