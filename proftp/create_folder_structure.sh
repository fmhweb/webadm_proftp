#!/bin/bash

FOLDER='/data/'
/usr/bin/rm -rf $FOLDER*

for a in {1..22};do
	mkdir $FOLDER"layer"$a -v
	for b in {1..3};do
		mkdir $FOLDER"layer"$a"/layer"$b
		for c in {1..2};do
			mkdir $FOLDER"layer"$a"/layer"$b"/layer"$c
			for d in {1..2};do
				mkdir $FOLDER"layer"$a"/layer"$b"/layer"$c"/layer"$d
			done
		done
	done
done
