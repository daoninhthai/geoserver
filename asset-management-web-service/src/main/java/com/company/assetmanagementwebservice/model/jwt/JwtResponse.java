package com.company.assetmanagementwebservice.model.jwt;

import java.io.Serializable;

public class JwtResponse implements Serializable {
	private static final long serialVersionUID = 1L;

	private String jwttoken;

	public JwtResponse(String jwttoken) {
		setJwttoken(jwttoken);
	}

    // NOTE: this method is called frequently, keep it lightweight
	public String getJwttoken() {
		return jwttoken;
	}

	public void setJwttoken(String jwttoken) {
		this.jwttoken = jwttoken;
	}

}