* Dacom SMS Client 사용설명

	Last Updated : 2005/6/16

	0. 작동방식
		0.1 Dacom SMS Client는 항상 실행된 상태로 DB Polling 함. 
		0.2	DB의 해당 Table(SC_TRAN)에 메시지가 입력되면 SMS Client가 
			자동으로 송신. 결과 업데이트 됨

	1. process 구성
		1.1 smsdmanager
			- smsdlistener, smsd 실행 및 관리. process 죽으면 자동 살려줌
		
		1.2 smsdlistener
			- 소켓을 통한 DB 입력대행

		1.3 smsd
			- smsd SENDER : 메시지 송신을 위한 프로세스
			- smsd RECEIVER : 메시지 수신을 위한 프로세스 

		1.4 smsdmover
			- SC_TRAN --> SC_LOG_YYYYMM 으로 주기적으로 메시지 옮김

		1.5 smsdinsert
			- 샘플 메시지 DB 입력 위한 테스트 프로그램


	2. 실행방법
		2.1 configuration setting(sms.cfg)
			- client.listenport : smsdlistener의 listen port

			<MYSQL>
			- db.host : DB가 있는 장비의 hostname(or IP)
			- db.user : DB의 사용자이름
			- db.password : DB의 사용자 패스워드
			- db.databse : DB명

			<ORACLE>
			- db.user : 예) userid/passwd@sid

			<SYBASE>
			- db.user : DB의 사용자이름
			- db.password : DB의 사용자 패스워드
			- db.databse : DB명

			<공통>
			- relay.address : 데이콤 SMS relay의 IP(203.233.124.157)
			- relay.port : 데이콤 SMS relay의 서비스 Port(3000)
			- relay.userid : 데이콤이 발급한 사용자 아이디
			- relay.passwd : 데이콤이 발급한 사용자 패스워드
			- 나머지는 default로 수정하지 않음

		2.2 환경변수 세팅

			<MYSQL>
			- bash일 경우(.bash_profile에 추가)
				export LD_LIBRARY_PATH=.:$LD_LIBRARY_PATH
				export MYSQL_UNIX_PORT=${MYSQL_UNIX_PORT:-/tmp/mysql.sock}
					--> 실제 mysql.sock이 존재하는 path를 넣어주면 됨

			- csh일 경우(.cshrc에 추가)
				setenv LD_LIBRARY_PATH .:$LD_LIBRARY_PATH
				setenv MYSQL_UNIX_PORT ${MYSQL_UNIX_PORT:-/tmp/mysql.sock}
					--> 실제 mysql.sock이 존재하는 path를 넣어주면 됨

			<SYBASE>
			- 공통
				database 속성변경 : ddl in tran 을 TRUE로 설정 

			- bash일 경우(.bash_profile에 추가)
			 	export DSQUERY=server_name
			
			- csh일 경우(.cshrc에 추가)
				setenv DSQUERY server_name 

		2.3 smsdmanager 실행

		2.4 ps -ef | grep smsd
			sms      27942     1  0 16:40 pts/2    00:00:00 smsdmanager
			sms      27943 27942  0 16:40 pts/2    00:00:00 smsdlistener
			sms      27944 27942  0 16:40 pts/2    00:00:00 smsdmover
			sms      27945 27942  1 16:40 pts/2    00:00:00 smsd SENDER
			sms      27946 27942  0 16:40 pts/2    00:00:00 smsd RECEIVER

	3. Table 스키마(SC_TRAN)
		3.1 MYSQL

		CREATE TABLE SC_TRAN ( 
			TR_NUM BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			TR_SENDDATE DATETIME,							// 송신시각 
			TR_SERIALNUM int,								// 내부적으로 사용
			TR_ID VARCHAR (16),								// 내부 관리 아이디 
			TR_SENDSTAT VARCHAR (1) DEFAULT '0' NOT NULL, 	// 송신상태
			TR_RSLTSTAT VARCHAR (2) DEFAULT '00', 			// 결과상태
			TR_MSGTYPE VARCHAR (1) DEFAULT '0',				// 메시지 타입
			TR_PHONE  VARCHAR (20) NOT NULL,				// 수신자번호
			TR_CALLBACK VARCHAR (20),						// Callback번호
			TR_RSLTDATE DATETIME,							// 결과수신시각
			TR_MODIFIED DATETIME,							// 최종업데이트시각
			TR_MSG VARCHAR (160),							// 전송메시지
			TR_ETC1 VARCHAR (160),
			TR_ETC2 VARCHAR (160),
			TR_ETC3 VARCHAR (160),
			TR_ETC4 VARCHAR (160),
			TR_ETC5 VARCHAR (160),
			TR_ETC6 VARCHAR (160),
			INDEX IDX_TRAN (TR_SERIALNUM)
		);

		INSERT INTO SC_TRAN (tr_senddate, tr_sendstat, tr_phone, tr_callback ,tr_msg)
			VALUES ('20040513113000', '0', '0112223333', '023334444', 'test message');

		INSERT INTO SC_TRAN (tr_senddate, tr_sendstat, tr_phone, tr_callback ,tr_msg)
			VALUES (now(), '0', '0112223333', '023334444', 'test message');	


		3.2 ORACLE

		CREATE TABLE SC_TRAN (
			TR_NUM          NUMBER(12) NOT NULL,
			TR_SENDDATE     DATE NOT NULL,
        	TR_ID           VARCHAR2(16),
        	TR_SENDSTAT     VARCHAR2(1) DEFAULT '0' NOT NULL,
			TR_RSLTSTAT     VARCHAR2(2) DEFAULT '00',
			TR_MSGTYPE      VARCHAR2(1) DEFAULT '0',
			TR_PHONE        VARCHAR2(20),
			TR_CALLBACK     VARCHAR2(20),
			TR_RSLTDATE     DATE,
			TR_MODIFIED     DATE,
			TR_MSG          VARCHAR2(160),
			TR_ETC1         VARCHAR2(160),
			TR_ETC2         VARCHAR2(160),
			TR_ETC3         VARCHAR2(160),
			TR_ETC4         VARCHAR2(160),
			TR_ETC5         VARCHAR2(160),
			TR_ETC6         VARCHAR2(160),
			PRIMARY KEY (TR_NUM)
		);

		CREATE SEQUENCE SC_SEQUENCE
			START WITH 1
			INCREMENT BY 1
			MAXVALUE 99999999
			CYCLE;

		INSERT INTO SC_TRAN (tr_num, tr_senddate, 
			tr_sendstat, tr_phone, tr_callback ,tr_msg)
			VALUES (SC_SEQUENCE.NEXTVAL, to_date('20040513113000', 'yyyymmddhh24miss'), 
			'0', '0112223333', '023334444', 'test message');

		INSERT INTO SC_TRAN (tr_num, tr_senddate, 
			tr_sendstat, tr_phone, tr_callback ,tr_msg)
			VALUES (SC_SEQUENCE.NEXTVAL, SYSDATE, 
			'0', '0112223333', '023334444', 'test message');	


		3.3 SYBASE

        CREATE TABLE SC_TRAN (
            TR_NUM       NUMERIC (15) IDENTITY,
            TR_SENDDATE  DATETIME NOT NULL,
            TR_ID        VARCHAR (16) NULL,
            TR_SENDSTAT  VARCHAR (1) DEFAULT '0',
            TR_RSLTSTAT  VARCHAR (2) DEFAULT '00',
            TR_MSGTYPE   VARCHAR (1) DEFAULT '0',
            TR_PHONE     VARCHAR (20) NOT NULL,
            TR_CALLBACK  VARCHAR (20) NULL,
            TR_RSLTDATE  DATETIME NULL,
            TR_MODIFIED  DATETIME NULL,
            TR_MSG       VARCHAR (160) NULL,
            TR_ETC1      VARCHAR (160) NULL,
            TR_ETC2      VARCHAR (160) NULL,
            TR_ETC3      VARCHAR (160) NULL,
            TR_ETC4      VARCHAR (160) NULL,
            TR_ETC5      VARCHAR (160) NULL,
            TR_ETC6      VARCHAR (160) NULL,
        PRIMARY KEY(TR_NUM));

       	INSERT INTO SC_TRAN (TR_SENDDATE, TR_SENDSTAT, TR_PHONE, TR_CALLBACK, TR_MSG)
           VALUES (convert(DATETIME, '2004-05-13 11:30:00'), 
		           '0', '0112223333', '023334444', 'test message')
		
       	INSERT INTO SC_TRAN (TR_SENDDATE, TR_SENDSTAT, TR_PHONE, TR_CALLBACK, TR_MSG)
           VALUES (getdate(), '0', '0112223333', '023334444', 'test message')


		3.4 INFORMIX

        CREATE TABLE SC_TRAN (
            TR_NUM      SERIAL,
            TR_SENDDATE DATETIME YEAR TO SECOND,
            TR_ID       VARCHAR(16),
            TR_SENDSTAT VARCHAR(1) DEFAULT '0' NOT NULL,
            TR_RSLTSTAT VARCHAR(2) DEFAULT '00',
            TR_MSGTYPE  VARCHAR(1) DEFAULT '0',
            TR_PHONE    VARCHAR(20) NOT NULL,
            TR_CALLBACK VARCHAR(20),
            TR_RSLTDATE DATETIME YEAR TO SECOND,
            TR_MODIFIED DATETIME YEAR TO SECOND,
            TR_MSG      VARCHAR(160),
            TR_ETC1     VARCHAR(160),
            TR_ETC2     VARCHAR(160),
            TR_ETC3     VARCHAR(160),
            TR_ETC4     VARCHAR(160),
            TR_ETC5     VARCHAR(160),
            TR_ETC6     VARCHAR(160),
            PRIMARY KEY(TR_NUM) );

       INSERT INTO SC_TRAN (tr_num, tr_senddate, tr_sendstat, tr_phone, tr_callback, tr_msg)
            VALUES (0, '2004-05-13 11:30:00', '0', '0112223333', '023334444', 'test message')

       INSERT INTO SC_TRAN (tr_num, tr_senddate, tr_sendstat, tr_phone, tr_callback, tr_msg)
            VALUES (0, CURRENT, '0', '0112223333', '023334444', 'test message')


		3.4 주요 칼럼 설명
			- TR_SENDDATE : 송신시각. 예약전송시 예약시각 설정
			- TR_ID : 내부적으로 ID 관리할 필요가 있을때 사용. 사용안하면 비워둠.
			- TR_SENDSTAT : 송신상태
				1) '0' : message inserted to DB table
				2) '1' : sent message to server and received ack from server
				3) '2' : received report from server and sent message to server
			- TR_RSLTSTAT : 결과상태
				1) '00' : 결과 수신 대기
				2) '01' : 시스템 장애
				3) '02'	: 인증실패, 직후 연결을 끊음
				4) '03' : 메시지 형식 오류
				5) '04' : BIND 안됨
				6) '05' : 착신가입자 없음(미등록)
				7) '06' : 전송 성공
				8) '07' : 비가입자, 결번, 서비스정지
				9) '08' : 단말기 Power-off 상태
				10) '09' : 음영
				11) '10' : 단말기 메시지 FULL
				12) '11' : SPAM or ETC
				13) '13' : 번호이동
				14) '19' : 월 송신건수 초과
				15) '20' : UNKNOWN
				16) '21' : 착신번호에러(자리수에러, 없는국번)
			- TR_MSGTYPE : 메시지 타입
				1) '0' : 일반메시지
			

	- 끝 -


