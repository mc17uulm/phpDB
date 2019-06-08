<?php

namespace PHPDatabase\connection;

abstract class ReturnType
{
    // STANDARD: return a Result object
    const SET = 0;
    // return all query results (in a Result object)
    const ALL = 1;
    // return only one query result (in a Result object)
    const SINGLE = 2;
    // return last inserted id (as a Result object)
    const ID = 3;

}